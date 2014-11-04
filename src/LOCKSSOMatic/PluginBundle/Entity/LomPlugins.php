<?php

namespace LOCKSSOMatic\PluginBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LomPlugins
 */
class LomPlugins
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
     * @var string
     */
    private $path;

    /**
     * @var boolean
     */
    private $enabled;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $pluginData;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->pluginData = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return LomPlugins
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
     * Set path
     *
     * @param string $path
     * @return LomPlugins
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return LomPlugins
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Add pluginData
     *
     * @param \LOCKSSOMatic\PluginBundle\Entity\LomPluginData $pluginData
     * @return LomPlugins
     */
    public function addPluginDatum(\LOCKSSOMatic\PluginBundle\Entity\LomPluginData $pluginData)
    {
        $this->pluginData[] = $pluginData;

        return $this;
    }

    /**
     * Remove pluginData
     *
     * @param \LOCKSSOMatic\PluginBundle\Entity\LomPluginData $pluginData
     */
    public function removePluginDatum(\LOCKSSOMatic\PluginBundle\Entity\LomPluginData $pluginData)
    {
        $this->pluginData->removeElement($pluginData);
    }

    /**
     * Get pluginData
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPluginData()
    {
        return $this->pluginData;
    }
}
