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
     * @return The Service Document.
     */
    public function serviceDocumentAction()
    {
        $request = Request::createFromGlobals();
        // Get value of 'On-Behalf-Of' HTTP header and include it as
        // the collection ID in the service document's col-iri parameter.
        $onBehalfOf = $request->headers->get('on_behalf_of');
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
            // Return a "Not Found" response.
            $response->setStatusCode(404);
            return $response;
        }

        $response = $this->render('LOCKSSOMaticSWORDBundle:Default:serviceDocument.xml.twig',
            array('onBehalfOf' => $onBehalfOf, 'name' => $contentProvider->getName()));
        $response->headers->set('Content-Type', 'text/xml');
        return $response;
    }

    /**
     * Controller for the Col-IRI (create resource) request.
     * 
     * @param integer $collectionID The SWORD Collection ID (same as the original On-Behalf-Of value).
     * @return The Deposit Receipt response.
     */
    public function createSubmissionAction($collectionId)
    {        
        // Get the request body.
        $request = new Request();
        $createResourceXml = $request->getContent();
        
        // Parse the Atom entry's <id> element, which will contain the deposit's UUID.
        $atomEntry = new \SimpleXMLElement($createResourceXml);
        $depositUuid = $atomEntry->id[0];
        $depositTitle = $atomEntry->title[0];
        // Remove the 'urn:uuid:'.
        $depositUuid = preg_replace('/^urn:uuid:/', '', $depositUuid);
        
        // Create a Deposit entity.
        $deposit = new Deposits();
        $deposit->setContentProvidersId($collectionId);
        $deposit->setUuid($depositUuid);
        $deposit->setTitle($depositTitle);
        $em = $this->getDoctrine()->getManager();
        $em->persist($deposit);
        $em->flush();
        
        // 2) Parse lom:content elements. We need the checksum type, checksum value,
        // file size, and URL.
        foreach($atomEntry->xpath('//lom:content') as $contentChunk) {
            foreach ($contentChunk[0]->attributes() as $key => $value) {
                // Create a new Content entity.
                $content = new Content();
                // @todo: Check to verify the content provider identified by
                // $collectionId exists. If not, return an appropriate error code.
                $content->setContentProvidersId($collectionId);
                $content->setDepositsId($deposit->getId());
                // @todo: Determine which AU the content should go into.
                // For now, use 1.
                $content->setAusId(1);
                $content->setUrl($contentChunk);                
                $content->setTitle('Some generatic title');
                $content->setSize($contentChunk[0]->attributes()->size);                
                $content->setChecksumType($contentChunk[0]->attributes()->checksumType);
                $content->setChecksumValue($contentChunk[0]->attributes()->checksumValue);
                $content->setReharvest(1);
                $cem = $this->getDoctrine()->getManager();
                $cem->persist($content);
                $cem->flush();
            }
        }

        // 3) Return the deposit receipt.
        $response = $this->render('LOCKSSOMaticSWORDBundle:Default:depositReceipt.xml.twig',
            array('contentProviderId' => $collectionId, 'depositUuid' => $deposit->getUuid()));
        $response->headers->set('Content-Type', 'text/xml');
        $response->setStatusCode(201);
        return $response;
    }

    /**
     * Controller for the SWORD Statement request.
     * 
     * @param integer $collectionID The SWORD Collection ID (same as the original On-Behalf-Of value).
     * @param string $uuid The UUID of the resource as provided by the content provider on resource creation.
     * 
     * @return The Statement response.
     */
    public function swordStatementAction($collectionId, $uuid)
    {
        // @todo: Get checksums from each box for each content.url.
        
        // Need to do a join here (select content.url from content, deposits where content.deposits_id =
        // deposits.id and deposits.uuid = $uuid.
        // SELECT content.url FROM content, deposits WHERE content.deposits_id = deposits.id AND
        // deposits.uuid = '1225c695-cfb8-4ebb-aaaa-80da344efa6a'
        $stmt = $this->getDoctrine()
                    ->getManager()
                    ->getConnection()
                    ->prepare('SELECT content.url FROM content, deposits WHERE content.deposits_id = deposits.id
                    AND deposits.uuid = :uuid');
        $stmt->bindValue('uuid', $uuid);
        $stmt->execute();
        $urls = $stmt->fetchAll();
        
        if (count($urls)) {
            $response = $this->render('LOCKSSOMaticSWORDBundle:Default:swordStatement.xml.twig',
                array('urls' => $urls));
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
     * @param integer $collectionID The SWORD Collection ID (same as the original On-Behalf-Of value).
     * @param string $uuid The UUID of the resource as provided by the content provider on resource creation.
     * 
     * @return The Edit-IRI response.
     */
    public function editSubmissionAction($collectionId, $uuid)
    {
         $logger = $this->get('logger');
        
        // Get the request body.
        $request = new Request();
        $editIriXml = $request->getContent();
        $logger->info($editIriXml);

        // Parse the 'recrawl' attribute of each lom:content element and update
        // the Content entity's 'recrawl' property if the value is false.
        // $atomEntry = new \SimpleXMLElement($editIriXml);
        $atomEntry = simplexml_load_string($editIriXml);
        foreach($atomEntry->xpath('//lom:content') as $contentChunk) {
            foreach ($contentChunk[0]->attributes() as $key => $value) {
                // Get the value of 'recrawl'.
                $recrawl = $contentChunk[0]->attributes()->recrawl;
                $logger->info('url is ' . $contentChunk);
                $logger->info('recrawl value is ' . $recrawl);
                if ($recrawl == 'false') {
                    $em = $this->getDoctrine()->getManager();
                    // Update the Content entity by finding its url value.
                    $content = $em
                        ->getRepository('LOCKSSOMatic\CRUDBundle\Entity\Content')
                        ->find($contentChunk);
                        if ($content) {
                            $logger->info('We found content');
                            $content->setRecrawl('0');
                            $em->flush();
                        }
                }
            }
        }
        
        // @todo: Each time a request is made to the Edit-IRI, check to see if all of the content
        // in the relevant AUs has a false is their recrawl properties, and if so, update
        // the AU properties with a 'pub_down' property and regenerate the AU's configuration
        // block in the title db file.
        
        $response = new Response();
        $response->setStatusCode(200);
        return $response;
    }
}
