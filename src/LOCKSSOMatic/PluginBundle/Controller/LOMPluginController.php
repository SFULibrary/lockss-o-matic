<?php

namespace LOCKSSOMatic\PluginBundle\Controller;

use LOCKSSOMatic\PluginBundle\Plugins\PluginsManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/plugin_manager")
 */
class LOMPluginController extends Controller
{
    /**
     * @Route("/", name="lom_plugin")
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
