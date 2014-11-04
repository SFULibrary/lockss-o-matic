<?php

namespace LOCKSSOMatic\PluginBundle\Plugins;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Base class for plugins.
 */
abstract class AbstractPlugin extends ContainerAware {
    
    /**
     * Get the name of the plugin.
     * 
     * @return string
     */
    abstract function getName();

    /**
     * Get a description of the plugin.
     * 
     * @return string
     */
    abstract function getDescription();
    
    public function __toString()
    {
        return $this->getName();
    }
}