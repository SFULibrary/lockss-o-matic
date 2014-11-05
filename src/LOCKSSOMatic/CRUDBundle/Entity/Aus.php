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
 * Aus
 */
class Aus
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $auid;

    /**
     * @var string
     */
    private $manifestUrl;

    /**
     * @var Collection
     */
    private $content;

    /**
     * @var Collection
     */
    private $auStatus;

    /**
     * @var Collection
     */
    private $auProperties;

    /**
     * @var Plns
     */
    private $pln;

    /**
     * @var Plugins
     */
    private $plugin;

    /**
     * @var boolean
     */
    private $managed;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->content = new ArrayCollection();
        $this->auStatus = new ArrayCollection();
        $this->auProperties = new ArrayCollection();
        $this->managed = true;
    }

    /**
     * Stringify the entity
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf('AU %d', array($this->id));
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
     * Set auid
     *
     * @param string $auid
     * @return Aus
     */
    public function setAuid($auid)
    {
        $this->auid = $auid;

        return $this;
    }

    /**
     * Get auid
     *
     * @return string
     */
    public function getAuid()
    {
        return $this->auid;
    }

    /**
     * Set manifestUrl
     *
     * @param string $manifestUrl
     * @return Aus
     */
    public function setManifestUrl($manifestUrl)
    {
        $this->manifestUrl = $manifestUrl;

        return $this;
    }

    /**
     * Get manifestUrl
     *
     * @return string
     */
    public function getManifestUrl()
    {
        return $this->manifestUrl;
    }

    /**
     * Add content
     *
     * @param Content $content
     * @return Aus
     */
    public function addContent(Content $content)
    {
        $this->content[] = $content;

        return $this;
    }

    /**
     * Remove content
     *
     * @param Content $content
     */
    public function removeContent(Content $content)
    {
        $this->content->removeElement($content);
    }

    /**
     * Get content
     *
     * @return Content
     */
    public function getContent()
    {
        return $this->content;
    }
    
    /**
     * Get the total size of the AU by adding the size of the 
     * content items. Returns size in kB (1,000 bytes).
     * 
     * @return int
     */
    public function getContentSize() {
        $size = 0;
        foreach($this->getContent() as $content) {
            $size += $content->getSize();
        }
        return $size;
    }

    /**
     * Add auStatus
     *
     * @param AuStatus $auStatus
     * @return Aus
     */
    public function addAuStatus(AuStatus $auStatus)
    {
        $this->auStatus[] = $auStatus;

        return $this;
    }

    /**
     * Remove auStatus
     *
     * @param AuStatus $auStatus
     */
    public function removeAuStatus(AuStatus $auStatus)
    {
        $this->auStatus->removeElement($auStatus);
    }

    /**
     * Get auStatus
     *
     * @return Collection
     */
    public function getAuStatus()
    {
        return $this->auStatus;
    }

    /**
     * Add auProperties
     *
     * @param AuProperties $auProperties
     * @return Aus
     */
    public function addAuProperty(AuProperties $auProperties)
    {
        $this->auProperties[] = $auProperties;

        return $this;
    }

    /**
     * Remove auProperties
     *
     * @param AuProperties $auProperties
     */
    public function removeAuProperty(AuProperties $auProperties)
    {
        $this->auProperties->removeElement($auProperties);
    }

    /**
     * Get auProperties
     *
     * @return Collection
     */
    public function getAuProperties()
    {
        return $this->auProperties;
    }

    /**
     * Set pln
     *
     * @param Plns $pln
     * @return Aus
     */
    public function setPln(Plns $pln = null)
    {
        $this->pln = $pln;

        return $this;
    }

    /**
     * Get pln
     *
     * @return Plns
     */
    public function getPln()
    {
        return $this->pln;
    }

    /**
     * Set plugin
     *
     * @param Plugins $plugin
     * @return Aus
     */
    public function setPlugin(Plugins $plugin = null)
    {
        $this->plugin = $plugin;

        return $this;
    }

    /**
     * Get plugin
     *
     * @return Plugins
     */
    public function getPlugin()
    {
        return $this->plugin;
    }

    /**
     * Set managed
     *
     * @param boolean $managed
     * @return Aus
     */
    public function setManaged($managed)
    {
        $this->managed = $managed;

        return $this;
    }

    /**
     * Get managed
     *
     * @return boolean 
     */
    public function getManaged()
    {
        return $this->managed;
    }
    /**
     * @var \LOCKSSOMatic\CRUDBundle\Entity\ContentProviders
     */
    private $contentProvider;


    /**
     * Set contentProvider
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\ContentProviders $contentProvider
     * @return Aus
     */
    public function setContentProvider(\LOCKSSOMatic\CRUDBundle\Entity\ContentProviders $contentProvider = null)
    {
        $this->contentProvider = $contentProvider;

        return $this;
    }

    /**
     * Get contentProvider
     *
     * @return \LOCKSSOMatic\CRUDBundle\Entity\ContentProviders 
     */
    public function getContentProvider()
    {
        return $this->contentProvider;
    }
}
