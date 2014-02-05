<?php

namespace LOCKSSOMatic\SWORDBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function serviceDocumentAction()
    {
        // @todo: Get value of proposed 'LOM-Content-Provider' HTTP header and include it as
        // the collection ID in the service document's col-iri parameter.
        $response = $this->render('LOCKSSOMaticSWORDBundle:Default:serviceDocument.xml.twig', array());
        $response->headers->set('Content-Type', 'text/xml');
        return $response;
    }

    public function createSubmissionAction($collectionId)
    {
        // @todo: Parse the Atom entry document and 1) add lom:content URLs to database,
        // 2) parse the Atom entry's <id>urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a</id>
        // element and add that to the deposits.uuid field, 3) return the deposit receipt.
        $response = $this->render('LOCKSSOMaticSWORDBundle:Default:depositReceipt.xml.twig', array());
        $response->headers->set('Content-Type', 'text/xml');
        $response->setStatusCode(201);
        return $response;
    }

    public function swordStatementAction($collectionId, $uuid)
    {
        // @todo: Parse the Atom entry document, look up the content URLs for the deposit UUID
        // in the request, and add these URLs to the lom:content URLs in the returned statement.
        // Also, get checksums from each box for the content.url.
        $response = $this->render('LOCKSSOMaticSWORDBundle:Default:swordStatement.xml.twig', array());
        $response->headers->set('Content-Type', 'text/xml');
        return $response;
    }

    public function editSubmissionAction($collectionId, $uuid)
    {
        // @todo: Parse the 'localVersion' (note: confirm this with Holly and Justin) and
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
