<?php

namespace LOCKSSOMatic\CRUDBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $contentProviders;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $aus;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $plnProperties;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $externalTitleDbs;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $boxes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->contentProviders = new \Doctrine\Common\Collections\ArrayCollection();
        $this->aus = new \Doctrine\Common\Collections\ArrayCollection();
        $this->plnProperties = new \Doctrine\Common\Collections\ArrayCollection();
        $this->externalTitleDbs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->boxes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Stringify the entity
     * 
     * @return string
     */
    public function __toString() {
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
     * @param \LOCKSSOMatic\CRUDBundle\Entity\ContentProviders $contentProviders
     * @return Plns
     */
    public function addContentProvider(\LOCKSSOMatic\CRUDBundle\Entity\ContentProviders $contentProviders)
    {
        $this->contentProviders[] = $contentProviders;

        return $this;
    }

    /**
     * Remove contentProviders
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\ContentProviders $contentProviders
     */
    public function removeContentProvider(\LOCKSSOMatic\CRUDBundle\Entity\ContentProviders $contentProviders)
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

    /**
     * Add aus
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\Aus $aus
     * @return Plns
     */
    public function addAus(\LOCKSSOMatic\CRUDBundle\Entity\Aus $aus)
    {
        $this->aus[] = $aus;

        return $this;
    }

    /**
     * Remove aus
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\Aus $aus
     */
    public function removeAus(\LOCKSSOMatic\CRUDBundle\Entity\Aus $aus)
    {
        $this->aus->removeElement($aus);
    }

    /**
     * Get aus
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAus()
    {
        return $this->aus;
    }

    /**
     * Add plnProperties
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\PlnProperties $plnProperties
     * @return Plns
     */
    public function addPlnProperty(\LOCKSSOMatic\CRUDBundle\Entity\PlnProperties $plnProperties)
    {
        $this->plnProperties[] = $plnProperties;

        return $this;
    }

    /**
     * Remove plnProperties
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\PlnProperties $plnProperties
     */
    public function removePlnProperty(\LOCKSSOMatic\CRUDBundle\Entity\PlnProperties $plnProperties)
    {
        $this->plnProperties->removeElement($plnProperties);
    }

    /**
     * Get plnProperties
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPlnProperties()
    {
        return $this->plnProperties;
    }

    /**
     * Add externalTitleDbs
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\ExternalTitleDbs $externalTitleDbs
     * @return Plns
     */
    public function addExternalTitleDb(\LOCKSSOMatic\CRUDBundle\Entity\ExternalTitleDbs $externalTitleDbs)
    {
        $this->externalTitleDbs[] = $externalTitleDbs;

        return $this;
    }

    /**
     * Remove externalTitleDbs
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\ExternalTitleDbs $externalTitleDbs
     */
    public function removeExternalTitleDb(\LOCKSSOMatic\CRUDBundle\Entity\ExternalTitleDbs $externalTitleDbs)
    {
        $this->externalTitleDbs->removeElement($externalTitleDbs);
    }

    /**
     * Get externalTitleDbs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getExternalTitleDbs()
    {
        return $this->externalTitleDbs;
    }

    /**
     * Add boxes
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\Boxes $boxes
     * @return Plns
     */
    public function addBox(\LOCKSSOMatic\CRUDBundle\Entity\Boxes $boxes)
    {
        $this->boxes[] = $boxes;

        return $this;
    }

    /**
     * Remove boxes
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\Boxes $boxes
     */
    public function removeBox(\LOCKSSOMatic\CRUDBundle\Entity\Boxes $boxes)
    {
        $this->boxes->removeElement($boxes);
    }

    /**
     * Get boxes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBoxes()
    {
        return $this->boxes;
    }
}
