<?php

namespace LOCKSSOMatic\PluginBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/plugin_manager")
 */
class LOMPluginController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        /** @var PluginsManager */
        $pluginManager = $this->get('lomplugin.manager');
        $plugins = $pluginManager->getPlugins();
        return array(
            'plugins' => $plugins
        );
    }
}
