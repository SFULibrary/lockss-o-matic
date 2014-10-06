<?php

namespace LOCKSSOMatic\CRUDBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PluginProperties
 */
class PluginProperties
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $propertyKey;

    /**
     * @var string
     */
    private $propertyValue;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $children;

    /**
     * @var \LOCKSSOMatic\CRUDBundle\Entity\Plugins
     */
    private $plugin;

    /**
     * @var \LOCKSSOMatic\CRUDBundle\Entity\PluginProperties
     */
    private $parent;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Stringify the entity
     * 
     * @return string
     */
    public function __toString() {
        return $this->propertyKey;
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
     * Set propertyKey
     *
     * @param string $propertyKey
     * @return PluginProperties
     */
    public function setPropertyKey($propertyKey)
    {
        $this->propertyKey = $propertyKey;

        return $this;
    }

    /**
     * Get propertyKey
     *
     * @return string 
     */
    public function getPropertyKey()
    {
        return $this->propertyKey;
    }

    /**
     * Set propertyValue
     *
     * @param string $propertyValue
     * @return PluginProperties
     */
    public function setPropertyValue($propertyValue)
    {
        $this->propertyValue = $propertyValue;

        return $this;
    }

    /**
     * Get propertyValue
     *
     * @return string 
     */
    public function getPropertyValue()
    {
        return $this->propertyValue;
    }

    /**
     * Add children
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\PluginProperties $children
     * @return PluginProperties
     */
    public function addChild(\LOCKSSOMatic\CRUDBundle\Entity\PluginProperties $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\PluginProperties $children
     */
    public function removeChild(\LOCKSSOMatic\CRUDBundle\Entity\PluginProperties $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set plugin
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\Plugins $plugin
     * @return PluginProperties
     */
    public function setPlugin(\LOCKSSOMatic\CRUDBundle\Entity\Plugins $plugin = null)
    {
        $this->plugin = $plugin;

        return $this;
    }

    /**
     * Get plugin
     *
     * @return \LOCKSSOMatic\CRUDBundle\Entity\Plugins 
     */
    public function getPlugin()
    {
        return $this->plugin;
    }

    /**
     * Set parent
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\PluginProperties $parent
     * @return PluginProperties
     */
    public function setParent(\LOCKSSOMatic\CRUDBundle\Entity\PluginProperties $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \LOCKSSOMatic\CRUDBundle\Entity\PluginProperties 
     */
    public function getParent()
    {
        return $this->parent;
    }
}
