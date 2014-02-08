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
            // $logger->info('URL: ' . $content);
            foreach ($contentChunk[0]->attributes() as $key => $value) {
                // Create a new Content entity.
                $content = new Content();
                // @todo: Content Provider must exist to get this value; use 1 for now.
                $content->setContentProvidersId(1);
                $content->setDepositsId($deposit->getId());
                // @todo: AU must exist to get this value; use 1 for now.
                $content->setAusId(1);
                $content->setUrl($contentChunk);                
                $content->setTitle('Some generatic title');
                $content->setSize($contentChunk[0]->attributes()->size);                
                // Date Added to AU will be empty until added to an AU.
                // $content->setDateAddedToAu();
                $content->setChecksumType($contentChunk[0]->attributes()->checksumType);
                $content->setChecksumValue($contentChunk[0]->attributes()->checksumValue);
                $content->setReharvest(1);
                $cem = $this->getDoctrine()->getManager();
                $cem->persist($content);
                $cem->flush();
            }
        }

        // 3) Return the deposit receipt.
        // @todo: Get the Content Provider ID. For now we use 1.
        $response = $this->render('LOCKSSOMaticSWORDBundle:Default:depositReceipt.xml.twig',
            array('contentProviderId' => '1', 'depositUuid' => $deposit->getUuid()));
        $response->headers->set('Content-Type', 'text/xml');
        $response->setStatusCode(201);
        return $response;
    }

    public function swordStatementAction($collectionId, $uuid)
    {
        // @todo: Look up the content URLs for the deposit UUID in the request, and add these URLs
        // to the lom:content URLs in the returned statement. Also, get checksums from each box for
        // the content.url.
        $response = $this->render('LOCKSSOMaticSWORDBundle:Default:swordStatement.xml.twig', array());
        $response->headers->set('Content-Type', 'text/xml');
        return $response;
    }

    public function editSubmissionAction($collectionId, $uuid)
    {
        // @todo: Parse the 'recrawl' attribute of each lom:content element and
        // determine if all of the files in the AU(s) have been previously flaggged as
        // ready to delete. If all files in the AU(s) have been flagged as ready to delete,
        // add a 'pub_down' attribute to the au_properties table for those AU(s).
        // Figure out how to return an empty body. swordStatement.xml.twig template
        // only here as placeholder.
        $response = $this->render('LOCKSSOMaticSWORDBundle:Default:swordStatement.xml.twig', array());
        $response->headers->set('Content-Type', 'text/xml');
        $response->setStatusCode(200);
        return $response;
    }
}
