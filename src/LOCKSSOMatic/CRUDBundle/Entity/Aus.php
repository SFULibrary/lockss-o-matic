<?php

namespace LOCKSSOMatic\CRUDBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $content;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $auStatus;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $auProperties;

    /**
     * @var \LOCKSSOMatic\CRUDBundle\Entity\Plns
     */
    private $pln;

    /**
     * @var \LOCKSSOMatic\CRUDBundle\Entity\Plugins
     */
    private $plugin;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->content = new \Doctrine\Common\Collections\ArrayCollection();
        $this->auStatus = new \Doctrine\Common\Collections\ArrayCollection();
        $this->auProperties = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Stringify the entity
     * 
     * @return string
     */
    public function __toString() {
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
     * @param \LOCKSSOMatic\CRUDBundle\Entity\Content $content
     * @return Aus
     */
    public function addContent(\LOCKSSOMatic\CRUDBundle\Entity\Content $content)
    {
        $this->content[] = $content;

        return $this;
    }

    /**
     * Remove content
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\Content $content
     */
    public function removeContent(\LOCKSSOMatic\CRUDBundle\Entity\Content $content)
    {
        $this->content->removeElement($content);
    }

    /**
     * Get content
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Add auStatus
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\AuStatus $auStatus
     * @return Aus
     */
    public function addAuStatus(\LOCKSSOMatic\CRUDBundle\Entity\AuStatus $auStatus)
    {
        $this->auStatus[] = $auStatus;

        return $this;
    }

    /**
     * Remove auStatus
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\AuStatus $auStatus
     */
    public function removeAuStatus(\LOCKSSOMatic\CRUDBundle\Entity\AuStatus $auStatus)
    {
        $this->auStatus->removeElement($auStatus);
    }

    /**
     * Get auStatus
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAuStatus()
    {
        return $this->auStatus;
    }

    /**
     * Add auProperties
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\AuProperties $auProperties
     * @return Aus
     */
    public function addAuProperty(\LOCKSSOMatic\CRUDBundle\Entity\AuProperties $auProperties)
    {
        $this->auProperties[] = $auProperties;

        return $this;
    }

    /**
     * Remove auProperties
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\AuProperties $auProperties
     */
    public function removeAuProperty(\LOCKSSOMatic\CRUDBundle\Entity\AuProperties $auProperties)
    {
        $this->auProperties->removeElement($auProperties);
    }

    /**
     * Get auProperties
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAuProperties()
    {
        return $this->auProperties;
    }

    /**
     * Set pln
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\Plns $pln
     * @return Aus
     */
    public function setPln(\LOCKSSOMatic\CRUDBundle\Entity\Plns $pln = null)
    {
        $this->pln = $pln;

        return $this;
    }

    /**
     * Get pln
     *
     * @return \LOCKSSOMatic\CRUDBundle\Entity\Plns 
     */
    public function getPln()
    {
        return $this->pln;
    }

    /**
     * Set plugin
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\Plugins $plugin
     * @return Aus
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
}
