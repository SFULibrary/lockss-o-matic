<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Plugin
 *
 * @ORM\Table(name="plugins")
 * @ORM\Entity
 */
class Plugin
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=128, nullable=false)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="path", type="string", length=255, nullable=false)
     */
    private $path;

    /**
     * @ORM\OneToMany(targetEntity="Au", mappedBy="plugin")
     * @var ArrayCollection
     */
    private $aus;

    /**
     * @ORM\OneToMany(targetEntity="ContentOwner", mappedBy="plugin")
     * @var ArrayCollection
     */
    private $contentOwners;

    /**
     * @ORM\OneToMany(targetEntity="PluginProperty", mappedBy="plugin")
     * @var ArrayCollection
     */
    private $pluginProperties;

    public function __construct() {
        $this->aus = new ArrayCollection();
        $this->contentOwners = new ArrayCollection();
        $this->$pluginProperties = new ArrayCollection();
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
     * @return Plugin
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
     * @return Plugin
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
     * Add aus
     *
     * @param Au $aus
     * @return Plugin
     */
    public function addAus(Au $aus)
    {
        $this->aus[] = $aus;

        return $this;
    }

    /**
     * Remove aus
     *
     * @param Au $aus
     */
    public function removeAus(Au $aus)
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
     * Add contentOwners
     *
     * @param ContentOwner $contentOwners
     * @return Plugin
     */
    public function addContentOwner(ContentOwner $contentOwners)
    {
        $this->contentOwners[] = $contentOwners;

        return $this;
    }

    /**
     * Remove contentOwners
     *
     * @param ContentOwner $contentOwners
     */
    public function removeContentOwner(ContentOwner $contentOwners)
    {
        $this->contentOwners->removeElement($contentOwners);
    }

    /**
     * Get contentOwners
     *
     * @return Collection
     */
    public function getContentOwners()
    {
        return $this->contentOwners;
    }

    /**
     * Add pluginProperties
     *
     * @param PluginProperty $pluginProperties
     * @return Plugin
     */
    public function addPluginProperty(PluginProperty $pluginProperties)
    {
        $this->pluginProperties[] = $pluginProperties;

        return $this;
    }

    /**
     * Remove pluginProperties
     *
     * @param PluginProperty $pluginProperties
     */
    public function removePluginProperty(PluginProperty $pluginProperties)
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
}
