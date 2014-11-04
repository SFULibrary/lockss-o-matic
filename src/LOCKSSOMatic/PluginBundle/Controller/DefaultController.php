<?php

namespace LOCKSSOMatic\PluginBundle\Controller;

use LOCKSSOMatic\PluginBundle\Plugins\PluginsManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        /** @var PluginsManager */
        $pluginManager = $this->get('lomplugin.manager');
        $plugins = $pluginManager->getPlugins();
        return $this->render('LOCKSSOMaticPluginBundle:Default:index.html.twig', array(
            'plugins' => $plugins
        ));
    }
}
