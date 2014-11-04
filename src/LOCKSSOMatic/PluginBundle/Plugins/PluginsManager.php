<?php

namespace LOCKSSOMatic\PluginBundle\Plugins;

use LOCKSSOMatic\PluginBundle\Plugins\AbstractPlugin;
use Symfony\Component\DependencyInjection\ContainerAware;

class PluginsManager extends ContainerAware
{
    /**
     * @var AbstractPlugin
     */
    private $plugins;

    public function __construct() {
        $this->plugins = array();
    }
    
    public function addPlugin(AbstractPlugin $plugin) {
        $this->plugins[] = $plugin;
    }
    
    public function getPlugins()
    {
        return $this->plugins;
    }

}
