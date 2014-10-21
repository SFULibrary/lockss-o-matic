<?php

namespace LOCKSSOMatic\CRUDBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Plns
 */
class Plns
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
    private $propsPath;

    /**
     * @var Collection
     */
    private $contentProviders;

    /**
     * @var Collection
     */
    private $aus;

    /**
     * @var Collection
     */
    private $plnProperties;

    /**
     * @var Collection
     */
    private $externalTitleDbs;

    /**
     * @var Collection
     */
    private $boxes;

    /**
     * @var string
     */
    private $propServer;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->contentProviders = new ArrayCollection();
        $this->aus = new ArrayCollection();
        $this->plnProperties = new ArrayCollection();
        $this->externalTitleDbs = new ArrayCollection();
        $this->boxes = new ArrayCollection();
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
     * @return Plns
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
     * Set propsPath
     *
     * @param string $propsPath
     * @return Plns
     */
    public function setPropsPath($propsPath)
    {
        $this->propsPath = $propsPath;

        return $this;
    }

    /**
     * Get propsPath
     *
     * @return string
     */
    public function getPropsPath()
    {
        return $this->propsPath;
    }

    /**
     * Add contentProviders
     *
     * @param ContentProviders $contentProviders
     * @return Plns
     */
    public function addContentProvider(ContentProviders $contentProviders)
    {
        $this->contentProviders[] = $contentProviders;

        return $this;
    }

    /**
     * Remove contentProviders
     *
     * @param ContentProviders $contentProviders
     */
    public function removeContentProvider(ContentProviders $contentProviders)
    {
        $this->contentProviders->removeElement($contentProviders);
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
     * Add aus
     *
     * @param Aus $aus
     * @return Plns
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
     * Add plnProperties
     *
     * @param PlnProperties $plnProperties
     * @return Plns
     */
    public function addPlnProperty(PlnProperties $plnProperties)
    {
        $this->plnProperties[] = $plnProperties;

        return $this;
    }

    /**
     * Remove plnProperties
     *
     * @param PlnProperties $plnProperties
     */
    public function removePlnProperty(PlnProperties $plnProperties)
    {
        $this->plnProperties->removeElement($plnProperties);
    }

    /**
     * Get plnProperties
     *
     * @return Collection
     */
    public function getPlnProperties()
    {
        return $this->plnProperties;
    }

    /**
     * Add externalTitleDbs
     *
     * @param ExternalTitleDbs $externalTitleDbs
     * @return Plns
     */
    public function addExternalTitleDb(ExternalTitleDbs $externalTitleDbs)
    {
        $this->externalTitleDbs[] = $externalTitleDbs;

        return $this;
    }

    /**
     * Remove externalTitleDbs
     *
     * @param ExternalTitleDbs $externalTitleDbs
     */
    public function removeExternalTitleDb(ExternalTitleDbs $externalTitleDbs)
    {
        $this->externalTitleDbs->removeElement($externalTitleDbs);
    }

    /**
     * Get externalTitleDbs
     *
     * @return Collection
     */
    public function getExternalTitleDbs()
    {
        return $this->externalTitleDbs;
    }

    /**
     * Add boxes
     *
     * @param Boxes $boxes
     * @return Plns
     */
    public function addBox(Boxes $boxes)
    {
        $this->boxes[] = $boxes;

        return $this;
    }

    /**
     * Remove boxes
     *
     * @param Boxes $boxes
     */
    public function removeBox(Boxes $boxes)
    {
        $this->boxes->removeElement($boxes);
    }

    /**
     * Get boxes
     *
     * @return Collection
     */
    public function getBoxes()
    {
        return $this->boxes;
    }

    /**
     * Set propServer
     *
     * @param string $propServer
     * @return Plns
     */
    public function setPropServer($propServer)
    {
        $this->propServer = $propServer;

        return $this;
    }

    /**
     * Get propServer
     *
     * @return string 
     */
    public function getPropServer()
    {
        return $this->propServer;
    }
}
