<?php

namespace LOCKSSOMatic\SWORDBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use LOCKSSOMatic\CRUDBundle\Entity\ContentProviders;
use LOCKSSOMatic\CRUDBundle\Entity\Deposits;
use LOCKSSOMatic\CRUDBundle\Entity\Content;

class DefaultController extends Controller
{
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
        // Get value of 'On-Behalf-Of' HTTP header (or its 'X-' version)
        // and include it as the collection ID in the service document's
        // col-iri parameter.
        if ($request->headers->has('x_on_behalf_of')) {
            $onBehalfOf = $request->headers->get('x_on_behalf_of');
        }
        elseif ($request->headers->has('on_behalf_of')) {
            $onBehalfOf = $request->headers->get('on_behalf_of');
        }
        else {
            $response = new Response();
            // Return a "Precondition Failed" response.
            $response->setStatusCode(412);
            return $response;
        }
        
        if (!is_numeric($onBehalfOf)) {
            $response = new Response();
            // Return a "Precondition Failed" response.
            $response->setStatusCode(412);
            return $response;
        }
 
        // Query the ContentProvider entity so we can get its name.
        $contentProvider = $this->getDoctrine()
            ->getRepository('LOCKSSOMatic\CRUDBundle\Entity\ContentProviders')
            ->find($onBehalfOf);
        
        if (!$contentProvider) {
            $response = new Response();
            // Return a "Forbidden" response.
            $response->setStatusCode(403);
            return $response;
        }

        $response = $this->render('LOCKSSOMaticSWORDBundle:Default:serviceDocument.xml.twig',
            array(
                'site_name' => $this->container->getParameter('site_name'),
                'base_url' => $this->container->getParameter('base_url'),          
                'onBehalfOf' => $onBehalfOf,
                'maxFileSize' => $contentProvider->getMaxFileSize(),
                'checksumType' => $contentProvider->getChecksumType(),
                'content_provider_name' => $contentProvider->getName())
            );
        $response->headers->set('Content-Type', 'text/xml');
        return $response;
    }

    /**
     * Controller for the Col-IRI (create resource) request.
     * 
     * @param integer $collectionID The SWORD Collection ID (same as the original On-Behalf-Of value).
     * @return string The Deposit Receipt response.
     */
    public function createDepositAction(Request $request, $collectionId)
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
        $createResourceXml = $request->getContent();
        
        // Get the value of the 'X-In-Progress' request header and define
        // whether we will be adding this deposit to an open or closed AU.
        if ($request->headers->has('in_progress')) {
            $inProgressHeaderValue = $request->headers->get('in_progress');
            if ($inProgressHeaderValue == 'true') {
                $inProgress = true;
            }
            else {
                $inProgress = false;
            }
        }
        elseif ($request->headers->has('x_in_progress')) {
            $inProgressHeaderValue = $request->headers->get('x_in_progress');
            if ($inProgressHeaderValue == 'true') {
                $inProgress = true;
            }
            else {
                $inProgress = false;
            }
        }
        // If there's no In-Progress/X-In-Progress header, we use a closed AU.
        else {
            $inProgress = false;
        }
        
        // Parse the Atom entry's <id> element, which will contain the deposit's UUID.
        $atomEntry = new \SimpleXMLElement($createResourceXml);
        $atomEntry->registerXPathNamespace('atom', 'http://www.w3.org/2005/Atom');
        $atomEntry->registerXPathNamespace('lom', 'http://lockssomatic.info/SWORD2');
        $atomEntry->registerXPathNamespace('dcterms', 'http://purl.org/dc/terms/');
        $atomNs = $atomEntry->children('http://www.w3.org/2005/Atom');
        $depositUuid = $atomNs->id[0];
        $depositTitle = $atomNs->title[0];
        // Remove the 'urn:uuid:'.
        $depositUuid = preg_replace('/^urn:uuid:/', '', $depositUuid);
        
        // Create a Deposit entity.
        $deposit = new Deposits();
        $deposit->setContentProvidersId($collectionId);
        $deposit->setUuid($depositUuid);
        $deposit->setTitle($depositTitle);
        $dem = $this->getDoctrine()->getManager();
        $dem->persist($deposit);
        $dem->flush();
        
        // Parse lom:content elements. We need the checksum type, checksum value,
        // file size, and URL.
        foreach($atomEntry->xpath('//lom:content') as $contentChunk) {
            foreach ($contentChunk[0]->attributes() as $key => $value) {
                $contentSize = $contentChunk[0]->attributes()->size;
                $contentChecksumType = $contentChunk[0]->attributes()->checksumType;
                $contentChecksumValue = $contentChunk[0]->attributes()->checksumValue;
            }
            // Create a new Content entity.
            $content = new Content();
            $content->setDeposit($deposit);
            $au = $this->getDestinationAu($inProgress, $collectionId, $contentSize);
            $content->setAu($au);
            $content->setUrl($contentChunk);                
            $content->setTitle('Some generatic title');
            $content->setSize($contentChunk[0]->attributes()->size);                
            $content->setChecksumType($contentChecksumType);
            $content->setChecksumValue($contentChecksumValue);
            $content->setRecrawl(1);
            $cem = $this->getDoctrine()->getManager();
            $cem->persist($content);
            $cem->flush();            
        }

        // Return the deposit receipt.
        $response = $this->render('LOCKSSOMaticSWORDBundle:Default:depositReceipt.xml.twig',
            array(
                'siteName' => $this->container->getParameter('site_name'),
                'baseUrl' => $this->container->getParameter('base_url'),
                'contentProviderId' => $collectionId,
                'depositUuid' => $deposit->getUuid())
            );
        $response->headers->set('Content-Type', 'text/xml');
        // Return the Edit-IRI in a Location header, as per the SWORD spec.
        $editIri = $this->container->getParameter('base_url') . '/api/sword/2.0/cont-iri/' . 
            $collectionId . '/' . $depositUuid . '/edit';
        $response->headers->set('Location', $editIri);
        $response->setStatusCode(201);
        return $response;
    }

    /**
     * Controller for the SWORD Statement request.
     * 
     * @param integer $collectionID The SWORD Collection ID (same as the original On-Behalf-Of value).
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
            $response = $this->render('LOCKSSOMaticSWORDBundle:Default:swordStatement.xml.twig',
                array('contentDetails' => $contentDetails));
            $response->headers->set('Content-Type', 'text/xml');                        
        }
        else {
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
        $atomEntry->registerXPathNamespace('lom', 'http://lockssomatic.info/SWORD2');
        $atomEntry->registerXPathNamespace('dcterms', 'http://purl.org/dc/terms/');
        foreach($atomEntry->xpath('//lom:content') as $contentChunk) {
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
                        }
                        else {
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
    public function depositReceiptAction($collectionId, $uuid) {
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
                'siteName' => $this->container->getParameter('site_name'),
                'baseUrl' => $this->container->getParameter('base_url'),
                'contentProviderId' => $collectionId,
                'depositUuid' => $uuid)
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
    public function getDestinationAu($inProgress, $collectionId, $contentSize) {
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
    public function confirmContentProvider($contentProviderId) {
        $cp = $this->getDoctrine()
            ->getRepository('LOCKSSOMatic\CRUDBundle\Entity\ContentProviders')
            ->find($contentProviderId);
        if ($cp) {
            return true;
        }
        else {
            return false;
        }
    }
}
