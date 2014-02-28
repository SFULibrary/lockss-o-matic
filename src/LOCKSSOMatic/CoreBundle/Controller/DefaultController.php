<?php

namespace LOCKSSOMatic\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('LOCKSSOMaticCoreBundle:Default:index.html.twig', array('name' => $name));
    }
}
