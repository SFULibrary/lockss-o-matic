<?php

namespace LOCKSSOMatic\PLNImporterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

#use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $response = $this->render('LOCKSSOMaticPLNImporterBundle:Default:index.html.twig', array());
        $response->headers->set('Content-Type', 'text/html');
        return $response;
    }
}
