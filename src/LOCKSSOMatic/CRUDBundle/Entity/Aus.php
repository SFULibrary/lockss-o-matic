<?php

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
     * @var integer
     */
    private $open;

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
     * Constructor
     */
    public function __construct()
    {
        $this->content = new ArrayCollection();
        $this->auStatus = new ArrayCollection();
        $this->auProperties = new ArrayCollection();
    }

    /**
     * Stringify the entity
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf('%d (%s)', array($this->id, $this->plugin->getName()));
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
     * Set open
     *
     * @param integer $open
     * @return Aus
     */
    public function setOpen($open)
    {
        $this->open = $open;

        return $this;
    }

    /**
     * Get open
     *
     * @return integer
     */
    public function getOpen()
    {
        return $this->open;
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
     * @return Collection
     */
    public function getContent()
    {
        return $this->content;
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
}
