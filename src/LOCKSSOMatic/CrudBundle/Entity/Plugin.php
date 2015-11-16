<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * LOCKSS Plugin
 *
 * @ORM\Table(name="plugins")
 * @ORM\Entity(repositoryClass="PluginRepository")
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
     * Name of the plugin.
     *
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=128)
     */
    private $name;

    /**
     * Path, in the local file system, to the plugin file (includes version number).
     *
     * @var string
     * @ORM\Column(name="path", type="string", length=255)
     */
    private $path;

    /**
     * Original file name for the plugin, does not include the version number.
     * @var string
     * @ORM\Column(name="filename", type="string", length=127)
     */
    private $filename;

    /**
     * Version number for the plugin, from the plugin's Xml config.
     * @var int
     * @ORM\Column(name="version", type="integer")
     */
    private $version;

    /**
     * Plugin identifier (an FQDN) from the plugin's Xml config.
     * @var string
     * @ORM\Column(name="identifier", type="string", length=255)
     */
    private $identifier;

    /**
     * AUs created for this plugin.
     *
     * @ORM\OneToMany(targetEntity="Au", mappedBy="plugin")
     * @var Au[]
     */
    private $aus;

    /**
     * Content owners which use the plugin.
     *
     * @ORM\OneToMany(targetEntity="ContentProvider", mappedBy="plugin")
     * @var ContentOwner[]
     */
    private $contentProviders;

    /**
     * Properties for the plugin.
     *
     * @ORM\OneToMany(targetEntity="PluginProperty", mappedBy="plugin")
     * @var PluginProperty[]
     */
    private $pluginProperties;

    public function __construct()
    {
        $this->aus = new ArrayCollection();
        $this->contentProviders = new ArrayCollection();
        $this->pluginProperties = new ArrayCollection();
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
     * Add contentProviders
     *
     * @param ContentProvider $contentProvider
     * @return Plugin
     */
    public function addContentProvider(ContentProvider $contentProvider)
    {
        $this->contentProviders[] = $contentProvider;

        return $this;
    }

    /**
     * Remove contentProviders
     *
     * @param ContentProvider $contentProvider
     */
    public function removeContentProvider(ContentProvider $contentProvider)
    {
        $this->contentProviders->removeElement($contentProvider);
    }

    /**
     * Get contentProviders
     *
     * @return Collection
     */
    public function getContentProviders()
    {
        return $this->contentProviders;
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

    /**
     * Get the top-most properties for the plugin.
     *
     * @return PluginProperty[]
     */
    public function getRootPluginProperties()
    {
        $properties = array();
        foreach ($this->pluginProperties as $p) {
            if ($p->hasParent()) {
                continue;
            }
            $properties[] = $p;
        }
        return $properties;
    }

    /**
     * Convenience method. Get the identifier from the plugin properties
     * if it is available, or the empty string.
     *
     * @return string
     */
    public function getPluginIdentifier()
    {
        foreach ($this->getPluginProperties() as $prop) {
            /** @var PluginProperties $prop */
            if ($prop->getPropertyKey() === 'plugin_identifier') {
                return $prop->getPropertyValue();
            }
        }
        return "";
    }

    /**
     * Get a list of the configparamdescr plugin properties.
     *
     * @return PluginProperties[]
     */
    public function getPluginConfigParams()
    {
        $properties = array();
        foreach ($this->getPluginProperties() as $prop) {
            /** @var PluginProperties $prop */
            if ($prop->getPropertyKey() !== 'plugin_config_props') {
                continue;
            }
            foreach ($prop->getChildren() as $child) {
                if ($child->getPropertyKey() !== 'configparamdescr') {
                    continue;
                }
                $properties[] = $child;
            }
        }
        return $properties;
    }

    /**
     * Get the value of one property for the plugin.
     *
     * @param string $propertyKey
     * @return string|null
     */
    public function getProperty($propertyKey)
    {
        foreach ($this->getPluginProperties() as $property) {
            if ($property->getPropertyKey() === $propertyKey) {
                return $property->getPropertyValue();
            }
        }
        return null;
    }

    /**
     * Convenience method. Get the definitional plugin parameter names
     *
     * @return array
     */
    public function getDefinitionalProperties()
    {
        $properties = array();

        foreach ($this->getPluginConfigParams() as $prop) {
            $key = '';
            $definitional = false;
            foreach ($prop->getChildren() as $child) {
                if ($child->getPropertyKey() === 'key') {
                    $key = $child->getPropertyValue();
                }
                if ($child->getPropertyKey() !== 'definitional') {
                    continue;
                }
                if ($child->getPropertyValue() === 'true') {
                    $definitional = true;
                }
            }
            if ($key !== '' && $definitional === true) {
                $properties[] = $key;
            }
        }

        return $properties;
    }

    /**
     * Get a string representation of the plugin.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Set filename
     *
     * @param string $filename
     * @return Plugin
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string 
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set version
     *
     * @param \int $version
     * @return Plugin
     */
    public function setVersion(\int $version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return \int 
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set identifier
     *
     * @param string $identifier
     * @return Plugin
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Get identifier
     *
     * @return string 
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }
}
