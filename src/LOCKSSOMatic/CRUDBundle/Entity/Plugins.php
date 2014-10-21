<?php

namespace LOCKSSOMatic\CRUDBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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
     * @var Collection
     */
    private $pluginProperties;

    /**
     * @var Collection
     */
    private $aus;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $contentProviders;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->pluginProperties = new ArrayCollection();
        $this->aus = new ArrayCollection();
    }
    
    /**
     * Stringify the entity
     *
     * @return string
     */
    public function __toString()
    {
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
     * @param PluginProperties $pluginProperties
     * @return Plugins
     */
    public function addPluginProperty(PluginProperties $pluginProperties)
    {
        $this->pluginProperties[] = $pluginProperties;

        return $this;
    }

    /**
     * Remove pluginProperties
     *
     * @param PluginProperties $pluginProperties
     */
    public function removePluginProperty(PluginProperties $pluginProperties)
    {
        $this->pluginProperties->removeElement($pluginProperties);
    }

    /**
     * Get pluginProperties
     *
     * @return Collection
     */
    public function getPluginProperties()
    {
        return $this->pluginProperties;
    }

    /**
     * Add aus
     *
     * @param Aus $aus
     * @return Plugins
     */
    public function addAus(Aus $aus)
    {
        $this->aus[] = $aus;

        return $this;
    }

    /**
     * Remove aus
     *
     * @param Aus $aus
     */
    public function removeAus(Aus $aus)
    {
        $this->aus->removeElement($aus);
    }

    /**
     * Get aus
     *
     * @return Collection
     */
    public function getAus()
    {
        return $this->aus;
    }

    /**
     * Add contentProviders
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\contentProviders $contentProviders
     * @return Plugins
     */
    public function addContentProvider(\LOCKSSOMatic\CRUDBundle\Entity\contentProviders $contentProviders)
    {
        $this->contentProviders[] = $contentProviders;

        return $this;
    }

    /**
     * Remove contentProviders
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\contentProviders $contentProviders
     */
    public function removeContentProvider(\LOCKSSOMatic\CRUDBundle\Entity\contentProviders $contentProviders)
    {
        $this->contentProviders->removeElement($contentProviders);
    }

    /**
     * Get contentProviders
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getContentProviders()
    {
        return $this->contentProviders;
    }
}
