<?php

namespace LOCKSSOMatic\SWORDBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function serviceDocumentAction()
    {
        $response = $this->render('LOCKSSOMaticSWORDBundle:Default:serviceDocument.xml.twig', array());
        $response->headers->set('Content-Type', 'text/xml');
        return $response;
    }

    public function createSubmissionAction($collectionId)
    {
        // @todo: Parse the Atom entry document and add lom:content URLs to database, etc.
        $response = $this->render('LOCKSSOMaticSWORDBundle:Default:depositReceipt.xml.twig', array());
        $response->headers->set('Content-Type', 'text/xml');
        $response->setStatusCode(201);
        return $response;
    }

    public function swordStatementAction($collectionId, $uuid)
    {
        // @todo: Parse the Atom entry document and add lom:content URLs to database, etc.
        $response = $this->render('LOCKSSOMaticSWORDBundle:Default:swordStatement.xml.twig', array());
        $response->headers->set('Content-Type', 'text/xml');
        return $response;
    }

    public function editSubmissionAction($collectionId, $uuid)
    {
        // @todo: Figure out how to return an empty body. swordStatement.xml.twig template
        // only here as placeholder.
        $response = $this->render('LOCKSSOMaticSWORDBundle:Default:swordStatement.xml.twig', array());
        $response->headers->set('Content-Type', 'text/xml');
        $response->setStatusCode(200);
        return $response;
    }
}
