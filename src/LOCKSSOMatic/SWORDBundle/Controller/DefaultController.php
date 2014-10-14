<?php

namespace LOCKSSOMatic\SWORDBundle\Controller;

use LOCKSSOMatic\CRUDBundle\Entity\Content;
use LOCKSSOMatic\CRUDBundle\Entity\ContentBuilder;
use LOCKSSOMatic\CRUDBundle\Entity\ContentProviders;
use LOCKSSOMatic\CRUDBundle\Entity\DepositBuilder;
use LOCKSSOMatic\CRUDBundle\Entity\Deposits;
use LOCKSSOMatic\SWORDBundle\Utilities\Namespaces;
use SimpleXMLElement;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{

    /**
     *
     * @var Namespaces
     */
    private $namespaces;

    public function __construct() {
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
            if (!is_null($value) && is_numeric($value)) {
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
            if (in_array($value,
                            array(
                        'true',
                        'false'))) {
                return $value;
            }
        }
        return false;
    }

    /**
     * 
     * @param type $onBehalfOf
     * @return ContentProviders
     */
    private function getContentProvider($onBehalfOf)
    {
        return $this->getDoctrine()->getRepository('LOCKSSOMaticCRUDBundle:ContentProviders')->find($onBehalfOf);
    }

    /**
     * Controller for the SWORD Service Document request.
     * 
     * The 'On-Behalf-Of' request header identifies the content provider.
     * This value is also used for the collection ID (in other words, each
     * content provider has its own SWORD collection).
     * 
     * @return string The Service Document.
     */
    public function serviceDocumentAction(Request $request)
    {
        $onBehalfOf = $this->getOnBehalfOfHeader($request);

        if (is_null($onBehalfOf)) {
            // Return a "Precondition Failed" response.
            return new Response('', Response::HTTP_PRECONDITION_FAILED);
        }

        // Query the ContentProvider entity so we can get its name.
        $contentProvider = $this->getContentProvider($onBehalfOf);

        if ($contentProvider === null) {
            // Return a "Forbidden" response.
            return new Response('', Response::HTTP_FORBIDDEN);
        }

        $response = $this->render('LOCKSSOMaticSWORDBundle:Default:serviceDocument.xml.twig',
                array(
            'site_name'             => $this->container->getParameter('site_name'),
            'base_url'              => $this->container->getParameter('base_url'),
            'onBehalfOf'            => $onBehalfOf,
            'maxFileSize'           => $contentProvider->getMaxFileSize(),
            'checksumType'          => $contentProvider->getChecksumType(),
            'content_provider_name' => $contentProvider->getName())
        );
        $response->headers->set('Content-Type', 'text/xml');
        return $response;
    }

    /**
     * Get a SimpleXMLElement from a string, and assign the necessary 
     * xpath namespaces.
     * 
     * @param string $xml
     * @return SimpleXMLElement
     */
    private function getSimpleXML($xml)
    {
        $xml = new SimpleXMLElement($xml);
        foreach (self::$namespaces as $prefix => $ns) {
            $xml->registerXPathNamespace($prefix, $ns);
        }
        return $xml;
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

        // Query the ContentProvider entity so we can get its name.
        $contentProvider = $this->getContentProvider($collectionId);

        if ($contentProvider === null) {
            // Return a "Forbidden" response.
            return new Response('', Response::HTTP_FORBIDDEN);
        }

        // Get the value of the 'X-In-Progress' request header and define
        // whether we will be adding this deposit to an open or closed AU.
        $inProgress = $this->getInProgressHeader($request);

        $atomEntry = $this->getSimpleXML($request->getContent());

        if (count($atomEntry->xpath('//lom:content')) === 0) {
            return new Response('', Response::HTTP_PRECONDITION_FAILED);
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

        // Return the deposit receipt.
        $response = $this->render('LOCKSSOMaticSWORDBundle:Default:depositReceipt.xml.twig',
                array(
            'siteName'          => $this->container->getParameter('site_name'),
            'baseUrl'           => $this->container->getParameter('base_url'),
            'contentProviderId' => $collectionId,
            'depositUuid'       => $deposit->getUuid())
        );
        $response->headers->set('Content-Type', 'text/xml');
        // Return the Edit-IRI in a Location header, as per the SWORD spec.
        $editIri = $this->get('router')->generate('lockssomatic_deposit_receipt',
                array(
            'collectionId' => $collectionId,
            'uuid'         => $deposit->getUuid()
        ));
        $response->headers->set('Location', $editIri);
        $response->setStatusCode(201);
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
                        'contentUrl'         => $contentItem['url'],
                        'serverId'           => $i,
                        'boxServeContentUrl' => 'http://lockss' . $i . '.example.org:8083/ServeContent?url=',
                        'checksumType'       => 'md5',
                        'checksumValue'      => 'fake9b64256fake754086de2fake6b7d',
                        'state'              => 'agreement'
                    );
                    $detailsForContentItems['boxes'][] = $boxDetails;
                }
                $contentDetails[] = array(
                    'contentUrl' => $contentItem['url'],
                    'boxes'      => $detailsForContentItems['boxes']
                );
            }
            $response = $this->render('LOCKSSOMaticSWORDBundle:Default:swordStatement.xml.twig',
                    array(
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
     * Controller for the Edit-IRI request.
     * 
     * LOCKSS-O-Matic supports only one edit operation: content providers can change the
     * value of the 'recrawl' attribute to indicate that LOM should not recrawl the content.
     * 
     * @todo: Add logic to return:
     * HTTP 200 (OK) meaning AU config stanzas have been updated.
     * HTTP 202 (Accepted) meaning LOM is updating the LOCKSS config files to prevent
     *     reharvest but it is not done yet.
     * HTTP 204 (No Content) if there is no matching Content URL.
     * HTTP 409 (Conflict) There are files in the LOCKSS AU that do not have ‘recrawl=false’.
     * 
     * @param integer $collectionID The SWORD Collection ID (same as the original On-Behalf-Of value).
     * @param string $uuid The UUID of the resource as provided by the content provider on resource creation.
     *   Not used in this function (is required as a parameter in the SWORD Edit-IRI).
     * @return object The Edit-IRI response.
     */
    public function editDepositAction($collectionId, $uuid)
    {
        // Check to verify the content provider identified by $collectionId
        // exists. If not, return an appropriate error code.
        $contentProviderExists = $this->confirmContentProvider($collectionId);
        if (!$contentProviderExists) {
            $response = new Response();
            $response->setStatusCode(403);
            return $response;
        }

        // Get the request body.
        $request = new Request();
        $editIriXml = $request->getContent();

        // Parse the 'recrawl' attribute of each lom:content element and update
        // the Content entity's 'recrawl' property if the value is false.
        $atomEntry = simplexml_load_string($editIriXml);
        $atomEntry->registerXPathNamespace('atom', 'http://www.w3.org/2005/Atom');
        $atomEntry->registerXPathNamespace('lom',
                'http://lockssomatic.info/SWORD2');
        $atomEntry->registerXPathNamespace('dcterms',
                'http://purl.org/dc/terms/');
        foreach ($atomEntry->xpath('//lom:content') as $contentChunk) {
            foreach ($contentChunk[0]->attributes() as $key => $value) {
                // Get the value of 'recrawl'.
                $recrawl = $contentChunk[0]->attributes()->recrawl;
                if ($recrawl == 'false') {
                    $em = $this->getDoctrine()->getManager();
                    // Update the Content entity by finding its url value.
                    $content = $em
                            ->getRepository('LOCKSSOMatic\CRUDBundle\Entity\Content')
                            ->findOneByUrl($contentChunk);
                    if ($content) {
                        $content->setRecrawl('0');
                        $em->flush();
                    } else {
                        $response = new Response();
                        // Return 204 No Content.
                        $response->setStatusCode(204);
                    }
                }
            }
        }

        // @todo: Each time a request is made to the Edit-IRI, check to see if all of the content
        // in the relevant AUs has a false is their recrawl properties, and if so, update
        // the AU properties with a 'pub_down' property and regenerate the AU's configuration
        // block in the title db file.

        $response = new Response();
        $response->setStatusCode(202);
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
        // Check to verify the content provider identified by $collectionId
        // exists. If not, return an appropriate error code.
        $contentProviderExists = $this->confirmContentProvider($collectionId);
        if (!$contentProviderExists) {
            $response = new Response();
            $response->setStatusCode(403);
            return $response;
        }

        // Return the deposit receipt.
        $response = $this->render('LOCKSSOMaticSWORDBundle:Default:depositReceipt.xml.twig',
                array(
            'siteName'          => $this->container->getParameter('site_name'),
            'baseUrl'           => $this->container->getParameter('base_url'),
            'contentProviderId' => $collectionId,
            'depositUuid'       => $uuid)
        );
        $response->headers->set('Content-Type', 'text/xml');
        return $response;
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
