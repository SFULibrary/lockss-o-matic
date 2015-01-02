<?php

/*
 * The MIT License
 *
 * Copyright (c) 2014 Mark Jordan, mjordan@sfu.ca.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

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
    private $contentOwners;

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
            foreach($prop->getChildren() as $child) {
                if($child->getPropertyKey() === 'key') {
                    $key = $child->getPropertyValue();
                }
                if($child->getPropertyKey() !== 'definitional') {
                    continue;
                }
                if($child->getPropertyValue() === 'true') {
                    $definitional = true;
                }
            }
            if($key !== '' && $definitional === true) {
                $properties[] = $key;
            }
        }

        return $properties;
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
     * Add contentOwners
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\ContentOwners $contentOwners
     * @return Plugins
     */
    public function addContentOwner(\LOCKSSOMatic\CRUDBundle\Entity\ContentOwners $contentOwners)
    {
        $this->contentOwners[] = $contentOwners;

        return $this;
    }

    /**
     * Remove contentOwners
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\ContentOwners $contentOwners
     */
    public function removeContentOwner(\LOCKSSOMatic\CRUDBundle\Entity\ContentOwners $contentOwners)
    {
        $this->contentOwners->removeElement($contentOwners);
    }

    /**
     * Get contentOwners
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getContentOwners()
    {
        return $this->contentOwners;
    }

}
