<?php

namespace LOCKSSOMatic\PluginBundle\Plugins;

use Doctrine\ORM\EntityManager;

/**
 * Base class for plugins.
 */
abstract class AbstractPlugin {
    
    /**
     * Plugin name, from the plugin.yml file.
     *
     * @var string
     */
    protected $name;

    /**
     * Enity manager for database access.
     *
     * @var EntityManager
     */
    protected $em;

    /**
     * Constructor using dependency injection
     * 
     * @param string $name
     * @param EntityManager $em
     */
    public function __construct($name, EntityManager $em)
    {
        $this->name = $name;
        $this->em = $em;
    }
    
    /**
     * Return the name of the plugin
     * 
     * @return string
     */
    public function getName() {
        return $this->name;
    }

}