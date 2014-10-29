<?php

namespace LOCKSSOMatic\PluginBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('LOCKSSOMaticPluginBundle:Default:index.html.twig', array('name' => $name));
    }
}
