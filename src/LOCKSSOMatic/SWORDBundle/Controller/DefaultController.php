<?php

namespace LOCKSSOMatic\SWORDBundle\Controller;

use LOCKSSOMatic\CRUDBundle\Entity\ContentBuilder;
use LOCKSSOMatic\CRUDBundle\Entity\ContentProviders;
use LOCKSSOMatic\CRUDBundle\Entity\DepositBuilder;
use LOCKSSOMatic\CRUDBundle\Entity\Deposits;
use LOCKSSOMatic\SWORDBundle\Utilities\Namespaces;
use SimpleXMLElement;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{

    /**
     *
     * @var Namespaces
     */
    private $namespaces;

    /**
     * Construct the controller.
     */
    public function __construct()
    {
        $this->namespaces = new Namespaces();
    }

    /**
     * Get the value of the X-On-Behalf-Of header (or it's equivalent), and
     * return it. Returns null if the header is not present, or is not a number.
     *
     * @param Request $request
     *
     * @return int the header value or null if not present.
     */
    private function getOnBehalfOfHeader(Request $request)
    {
        $headers = array(
            'x-on-behalf-of',
            'on-behalf-of'
        );
        foreach ($headers as $h) {
            $value = $request->headers->get($h);
            if (!is_null($value)) {
                return $value;
            }
        }
        return null;
    }

    /**
     * Get the value of the in-progress HTTP header, which must be either
     * true or false. Returns false if the header is not present or is a
     * value other than true or false.
     *
     * @param Request $request
     * @return boolean
     */
    private function getInProgressHeader(Request $request)
    {
        $headers = array(
            'x-in-progress',
            'in-progress'
        );
        foreach ($headers as $h) {
            $value = $request->headers->get($h);
            if (in_array($value, array(
                    'true',
                    'false'))) {
                return $value == 'true';
            }
        }
        return false;
    }

    /**
     * Get a SimpleXMLElement from a string, and assign the necessary
     * xpath namespaces.
     *
     * @param string $string
     * @return SimpleXMLElement
     */
    private function getSimpleXML($string)
    {
        $xml = new SimpleXMLElement($string);
        $this->namespaces->registerNamespaces($xml);
        return $xml;
    }

    /**
     *
     * @param type $onBehalfOf
     * @return ContentProviders
     */
    private function getContentProvider($onBehalfOf = null)
    {
        if ($onBehalfOf === null) {
            return null;
        }
        return $this->getDoctrine()->getRepository('LOCKSSOMaticCRUDBundle:ContentProviders')->find($onBehalfOf);
    }

    /**
     * Controller for the SWORD Service Document request.
     *
     * The 'On-Behalf-Of' request header identifies the content provider.
     * This value is also used for the collection ID (in other words, each
     * content provider has its own SWORD collection).
     *
     * @return Response The Service Document.
     */
    public function serviceDocumentAction(Request $request)
    {
        $onBehalfOf = $this->getOnBehalfOfHeader($request);
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $response->setStatusCode(Response::HTTP_OK);
        
        if (is_null($onBehalfOf)) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            return $this->render('LOCKSSOMaticSWORDBundle:Default:errorDocument.xml.twig', array(
                    'error_iri' => 'http://purl.org/net/sword/error/ErrorBadRequest',
                    'summary' => 'On-Behalf-Of header missing.',
                    'verbose' => 'LOCKSSOMatic requires mediated deposit via the On-Behalf-Of HTTP header. '
                ), $response);
        }

        $contentProvider = $this->getContentProvider($onBehalfOf);
        if ($onBehalfOf !== null && $contentProvider === null) {
            $response->setStatusCode(Response::HTTP_FORBIDDEN);
            return $this->render('LOCKSSOMaticSWORDBundle:Default:errorDocument.xml.twig', array(
                    'error_iri' => 'http://purl.org/net/sword/error/TargetOwnerUnknown',
                    'summary' => 'Unknown ID in the On-Behalf-Of header.',
                    'verbose' => 'The On-Behalf-Of HTTP header is present in the request but references an unknown content provider: ' . $onBehalfOf
                ), $response);
        }

        if ($response->getStatusCode() === Response::HTTP_OK) {
            return $this->render(
                'LOCKSSOMaticSWORDBundle:Default:serviceDocument.xml.twig', array(
                    'contentProvider' => $contentProvider,
                ), $response
            );
        }
        
    }

    /**
     * Controller for the Col-IRI (create resource) request.
     *
     * @param integer $collectionID The SWORD Collection ID (same as the original On-Behalf-Of value).
     * @return string The Deposit Receipt response.
     */
    public function createDepositAction(Request $request, $collectionId)
    {
        $em = $this->getDoctrine()->getManager();
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $response->setStatusCode(Response::HTTP_OK);

        // Query the ContentProvider entity so we can get its name.
        $contentProvider = $this->getContentProvider($collectionId);
        if ($contentProvider === null) {
            $response->setStatusCode(Response::HTTP_FORBIDDEN);
            return $this->render('LOCKSSOMaticSWORDBundle:Default:errorDocument.xml.twig', array(
                    'error_iri' => 'http://purl.org/net/sword/error/TargetOwnerUnknown',
                    'summary' => 'Unknown content provider in URL.',
                    'verbose' => 'The content provider identifed in the deposit URL is unknown.'
                ), $response);
        }
        // Get the value of the 'X-In-Progress' request header and define
        // whether we will be adding this deposit to an open or closed AU.
        $inProgress = $this->getInProgressHeader($request);

        $atomEntry = $this->getSimpleXML($request->getContent());
        if (count($atomEntry->xpath('//lom:content')) === 0) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            return $this->render('LOCKSSOMaticSWORDBundle:Default:errorDocument.xml.twig', array(
                    'error_iri' => 'http://purl.org/net/sword/error/ErrorBadRequest',
                    'summary' => 'Empty deposits not allowed.',
                    'verbose' => 'At least one lom:content element is required in a deposit.'
                ), $response);
        }

        $depositBuilder = new DepositBuilder();
        $deposit = $depositBuilder->fromSimpleXML($atomEntry);
        $deposit->setContentProvider($contentProvider);
        $em->persist($deposit);
        $em->flush();

        // Parse lom:content elements. We need the checksum type, checksum value,
        // file size, and URL.
        $contentBuilder = new ContentBuilder();
        foreach ($atomEntry->xpath('//lom:content') as $contentChunk) {
            // Create a new Content entity.
            $content = $contentBuilder->fromSimpleXML($contentChunk);
            $content->setDeposit($deposit);
            $au = $this->getDestinationAu($inProgress, $collectionId, $contentChunk[0]->attributes()->size);
            $content->setAu($au);
            $content->setRecrawl(1);
            $em->persist($content);
            $em->flush();
        }

        $response = $this->renderDepositReceipt($contentProvider, $deposit);
        $editIri = $this->get('router')->generate('lockssomatic_deposit_receipt', array(
            'collectionId' => $collectionId,
            'uuid' => $deposit->getUuid()
        ));
        $response->headers->set('Location', $editIri);
        $response->setStatusCode(201);
        return $response;
    }

    /**
     * Returns a deposit receipt, in response to a request to the SWORD Edit-IRI.
     *
     * @param integer $collectionID The SWORD Collection ID (same as the original On-Behalf-Of value).
     * @param string $uuid The UUID of the resource as provided by the content provider on resource creation.
     * @return string The Deposit Receipt response.
     */
    public function depositReceiptAction($collectionId, $uuid)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $response->setStatusCode(Response::HTTP_OK);
        
        $contentProvider = $this->getContentProvider($collectionId);
        if ($contentProvider === null) {
            $response->setStatusCode(Response::HTTP_FORBIDDEN);
            return $this->render('LOCKSSOMaticSWORDBundle:Default:errorDocument.xml.twig', array(
                    'error_iri' => 'http://purl.org/net/sword/error/TargetOwnerUnknown',
                    'summary' => 'Unknown content provider in URL.',
                    'verbose' => 'The content provider identifed in the deposit URL is unknown.'
                ), $response);
        }
        
        $deposit = $this->getDoctrine()
            ->getRepository('LOCKSSOMatic\CRUDBundle\Entity\Deposits')
            ->findOneBy(array('uuid' => $uuid));
        if ($deposit === null) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            return $this->render('LOCKSSOMaticSWORDBundle:Default:errorDocument.xml.twig', array(
                    'error_iri' => 'http://purl.org/net/sword/error/ErrorBadRequest',
                    'summary' => 'Unknown deposit.',
                    'verbose' => 'The deposit requested in the URL does not exist.'
                ), $response);
        }
        
        if ($deposit->getContentProvider()->getId() !== $contentProvider->getId()) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            return $this->render('LOCKSSOMaticSWORDBundle:Default:errorDocument.xml.twig', array(
                    'error_iri' => 'http://purl.org/net/sword/error/ErrorBadRequest',
                    'summary' => 'Deposit or Content Provider incorrect.',
                    'verbose' => 'The requested deposit does not belong to the requested content provider.'
                ), $response);
        }

        return $this->renderDepositReceipt($contentProvider, $deposit);
    }

    private function renderDepositReceipt($contentProvider, $deposit)
    {
        // @TODO this should be a call to render depsoitReceiptAction() or something.
        // Return the deposit receipt.
        $response = $this->render(
            'LOCKSSOMaticSWORDBundle:Default:depositReceipt.xml.twig', array(
            'contentProvider' => $contentProvider,
            'deposit' => $deposit
            )
        );
        $response->headers->set('Content-Type', 'text/xml');
        return $response;
    }

    /**
     * Controller for the SWORD Statement request.
     *
     * @param integer $collectionId The SWORD Collection ID (same as the original On-Behalf-Of value).
     * @param string $uuid The UUID of the resource as provided by the content provider on resource creation.
     * @return string The Statement response.
     */
    public function swordStatementAction($collectionId, $uuid)
    {
        // Check to verify the content provider identified by $collectionId
        // exists. If not, return an appropriate error code.
        $contentProviderExists = $this->confirmContentProvider($collectionId);
        if (!$contentProviderExists) {
            $response = new Response();
            $response->setStatusCode(403);
            return $response;
        }

        // Get the URLs for all the Content chunks added in the deposit identifed
        // by $uuid.
        $stmt = $this->getDoctrine()
            ->getManager()
            ->getConnection()
            ->prepare('SELECT DISTINCT content.id, content.url FROM content, deposits WHERE
                    content.deposits_id = deposits.id AND deposits.uuid = :uuid');
        $stmt->bindValue('uuid', $uuid);
        $stmt->execute();
        $content = $stmt->fetchAll();

        if (count($content)) {
            $contentDetails = array();
            foreach ($content as $contentItem) {
                $detailsForContentItems = array();
                // Generate placeholder values for 6 servers in the PLN.
                // @todo: Query each server in the PLN for the real values.
                for ($i = 1; $i <= 6; $i++) {
                    $boxDetails = array(
                        'contentUrl' => $contentItem['url'],
                        'serverId' => $i,
                        'boxServeContentUrl' => 'http://lockss' . $i . '.example.org:8083/ServeContent?url=',
                        'checksumType' => 'md5',
                        'checksumValue' => 'fake9b64256fake754086de2fake6b7d',
                        'state' => 'agreement'
                    );
                    $detailsForContentItems['boxes'][] = $boxDetails;
                }
                $contentDetails[] = array(
                    'contentUrl' => $contentItem['url'],
                    'boxes' => $detailsForContentItems['boxes']
                );
            }
            $response = $this->render('LOCKSSOMaticSWORDBundle:Default:swordStatement.xml.twig', array(
                'contentDetails' => $contentDetails));
            $response->headers->set('Content-Type', 'text/xml');
        } else {
            // Return a "Not Found" response.
            $response = new Response();
            $response->setStatusCode(404);
        }
        return $response;
    }

    /**
     * Get an ATOM XML representation of the deposit, suitable for PUTting
     * after an edit.
     *
     * Section 6.4 of the SWORD spec.
     *
     * @param Request $request
     * @param integer $collectionId
     * @param string $uuid
     */
    public function viewDepositAction(Request $request, $collectionId, $uuid)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $response->setStatusCode(Response::HTTP_OK);

        $contentProvider = $this->getContentProvider($collectionId);
        if ($contentProvider === null) {
            $response->setStatusCode(Response::HTTP_FORBIDDEN);
            return $this->render('LOCKSSOMaticSWORDBundle:Default:errorDocument.xml.twig', array(
                    'error_iri' => 'http://purl.org/net/sword/error/TargetOwnerUnknown',
                    'summary' => 'Unknown content provider in URL.',
                    'verbose' => 'The content provider identifed in the deposit URL is unknown.'
                ), $response);
        }
        
        /** @var Deposits */
        $deposit = $this->getDoctrine()
            ->getRepository('LOCKSSOMatic\CRUDBundle\Entity\Deposits')
            ->findOneBy(array('uuid' => $uuid));
        if ($deposit === null) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            return $this->render('LOCKSSOMaticSWORDBundle:Default:errorDocument.xml.twig', array(
                    'error_iri' => 'http://purl.org/net/sword/error/ErrorBadRequest',
                    'summary' => 'Unknown deposit.',
                    'verbose' => 'The deposit requested in the URL does not exist.'
                ), $response);
        }
        
        if ($deposit->getContentProvider()->getId() !== $contentProvider->getId()) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            return $this->render('LOCKSSOMaticSWORDBundle:Default:errorDocument.xml.twig', array(
                    'error_iri' => 'http://purl.org/net/sword/error/ErrorBadRequest',
                    'summary' => 'Deposit or Content Provider incorrect.',
                    'verbose' => 'The requested deposit does not belong to the requested content provider.'
                ), $response);
        }

        $response = $this->render(
            'LOCKSSOMaticSWORDBundle:Default:viewDeposit.xml.twig', array(
            'contentProvider' => $contentProvider,
            'deposit' => $deposit
            )
        );
        $response->headers->set('Content-Type', 'text/xml');
        return $response;
    }

    /**
     * Controller for the Edit-IRI request.
     * Section 6.5.2 of SWORDv2-Profile
     *
     * http://swordapp.github.io/SWORDv2-Profile/SWORDProfile.html#protocoloperations_editingcontent_metadata
     *
     * LOCKSS-O-Matic supports only one edit operation: content providers can change the
     * value of the 'recrawl' attribute to indicate that LOM should not recrawl the content.
     *
     * HTTP 200 (OK) meaning AU config stanzas have been updated.
     * HTTP 204 (No Content) if there is no matching Content URL.
     *
     * @param integer $collectionID The SWORD Collection ID (same as the original On-Behalf-Of value).
     * @param string $uuid The UUID of the resource as provided by the content provider on resource creation.
     * @return object The Edit-IRI response.
     */
    public function editDepositAction(Request $request, $collectionId, $uuid)
    {
        $em = $this->getDoctrine()->getManager();
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $response->setStatusCode(Response::HTTP_OK);

        $contentProvider = $this->getContentProvider($collectionId);
        if ($contentProvider === null) {
            $response->setStatusCode(Response::HTTP_FORBIDDEN);
            return $this->render('LOCKSSOMaticSWORDBundle:Default:errorDocument.xml.twig', array(
                    'error_iri' => 'http://purl.org/net/sword/error/TargetOwnerUnknown',
                    'summary' => 'Unknown content provider in URL.',
                    'verbose' => 'The content provider identifed in the deposit URL is unknown.'
                ), $response);
        }
        
        /** @var Deposits */
        $deposit = $this->getDoctrine()
            ->getRepository('LOCKSSOMatic\CRUDBundle\Entity\Deposits')
            ->findOneBy(array('uuid' => $uuid));
        if ($deposit === null) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            return $this->render('LOCKSSOMaticSWORDBundle:Default:errorDocument.xml.twig', array(
                    'error_iri' => 'http://purl.org/net/sword/error/ErrorBadRequest',
                    'summary' => 'Unknown deposit.',
                    'verbose' => 'The deposit requested in the URL does not exist.'
                ), $response);
        }
        
        if ($deposit->getContentProvider()->getId() !== $contentProvider->getId()) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            return $this->render('LOCKSSOMaticSWORDBundle:Default:errorDocument.xml.twig', array(
                    'error_iri' => 'http://purl.org/net/sword/error/ErrorBadRequest',
                    'summary' => 'Deposit or Content Provider incorrect.',
                    'verbose' => 'The requested deposit does not belong to the requested content provider.'
                ), $response);
        }

        $atomEntry = $this->getSimpleXML($request->getContent());
        $updated = 0;
        
        foreach ($atomEntry->xpath('//lom:content') as $contentChunk) {
            $content = $em->getRepository('LOCKSSOMaticCRUDBundle:Content')->findOneBy(
                array(
                    'url' => (string)$contentChunk,
                    'deposit' => $deposit
                )
            );
            if ($content === null) {
                continue;
            }
            $recrawl = $contentChunk[0]->attributes()->recrawl;
            $content->setRecrawl($recrawl === 'true');
            $updated++;
        }
        
        $em->flush();
        if ($updated > 0) {
            return new Response('', Response::HTTP_OK);
        } else {
            return new Response('', Response::HTTP_NO_CONTENT);
        }
    }

    /**
     * Determines which AU to put the content in.
     *
     * @param bool $inProgress Whether the AU is 'open' or 'closed'.
     * @param string $collectionId The collection ID (i.e., Content Provider ID).
     *   We use this to determine some AU properties like title, journal title, etc.
     * @param string $contentSize The size of the content, in kB.
     * @return object $au.
     */
    public function getDestinationAu($inProgress, $collectionId, $contentSize)
    {
        // @todo: For open AUs, if $contentSize is less than remaining capacity
        // of the newest AU for the Content Provider, put the content in this AU.
        // If $contentSize is greater, create a new AU and put the content in this
        // one. For closed AUs, create a new AU and put all content in this deposit
        // in it.
        //
        // Query for the Au. For now, just pick the Au with id 1.
        $au = $this->getDoctrine()
            ->getRepository('LOCKSSOMatic\CRUDBundle\Entity\Aus')
            ->find(1);
        return $au;
    }

    /**
     * Confirm existence of content provider.
     *
     * @param bool $contentProviderId The ID of the content provider.
     * @return bool
     */
    public function confirmContentProvider($contentProviderId)
    {
        $cp = $this->getDoctrine()
            ->getRepository('LOCKSSOMatic\CRUDBundle\Entity\ContentProviders')
            ->find($contentProviderId);
        if ($cp) {
            return true;
        } else {
            return false;
        }
    }
}
