<?php

namespace LOCKSSOMatic\CRUDBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Plugins
 */
class Plugins
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $pluginProperties;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $aus;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->pluginProperties = new \Doctrine\Common\Collections\ArrayCollection();
        $this->aus = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Stringify the entity
     * 
     * @return string
     */
    public function __toString() {
        return $this->name;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Plugins
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add pluginProperties
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\PluginProperties $pluginProperties
     * @return Plugins
     */
    public function addPluginProperty(\LOCKSSOMatic\CRUDBundle\Entity\PluginProperties $pluginProperties)
    {
        $this->pluginProperties[] = $pluginProperties;

        return $this;
    }

    /**
     * Remove pluginProperties
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\PluginProperties $pluginProperties
     */
    public function removePluginProperty(\LOCKSSOMatic\CRUDBundle\Entity\PluginProperties $pluginProperties)
    {
        $this->pluginProperties->removeElement($pluginProperties);
    }

    /**
     * Get pluginProperties
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPluginProperties()
    {
        return $this->pluginProperties;
    }

    /**
     * Add aus
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\Aus $aus
     * @return Plugins
     */
    public function addAus(\LOCKSSOMatic\CRUDBundle\Entity\Aus $aus)
    {
        $this->aus[] = $aus;

        return $this;
    }

    /**
     * Remove aus
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\Aus $aus
     */
    public function removeAus(\LOCKSSOMatic\CRUDBundle\Entity\Aus $aus)
    {
        $this->aus->removeElement($aus);
    }

    /**
     * Get aus
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAus()
    {
        return $this->aus;
    }
}
