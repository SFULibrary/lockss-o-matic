<?php

namespace LOCKSSOMatic\PLNExporterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('LOCKSSOMaticPLNExporterBundle:Default:index.html.twig', array('name' => $name));
    }
}
