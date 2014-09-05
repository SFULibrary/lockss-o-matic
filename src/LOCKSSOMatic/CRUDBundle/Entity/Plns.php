<?php

namespace LOCKSSOMatic\CRUDBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Plns
 *
 * @ORM\Table(name="plns")
 * @ORM\Entity
 */
class Plns
{
	/**
	 * Property required for one-to-many relationship with Aus.
	 * 
	 * @ORM\OneToMany(targetEntity="Aus", mappedBy="aus")
	 */
	protected $aus;

	/**
	 * Property required for one-to-many relationship with PlnProperties.
	 * 
	 * @ORM\OneToMany(targetEntity="PlnProperties", mappedBy="plnProperties")
	 */
	protected $plnProperties;

	/**
	 * Property required for one-to-many relationship with ExternalTitleDbs.
	 * 
	 * @ORM\OneToMany(targetEntity="ExternalTitleDbs", mappedBy="externalTitleDbs")
	 */
	protected $externalTitleDbs;

	/**
	 * Property required for one-to-many relationship with Boxes.
	 * 
	 * @ORM\OneToMany(targetEntity="Boxes", mappedBy="boxes")
	 */
	protected $boxes;

	/**
	 * Property required for one-to-many relationship with ContentProviders.
	 * 
	 * @ORM\OneToMany(targetEntity="ContentProviders", mappedBy="contentProviders")
	 */
	protected $contentProviders;

	/**
	 * Initializes the $aus, $plnProperties, $externalTitleDbs, $boxes, and $contentProviders
	 * properties.
	 */

	public function __construct()
	{
		$this->aus = new ArrayCollection();
        $this->plnProperties = new ArrayCollection();
        $this->externalTitleDbs = new ArrayCollection();
        $this->boxes = new ArrayCollection();
        $this->contentProviders = new ArrayCollection();
	}

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
     * @ORM\Column(name="name", type="text", nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="props_path", type="text", nullable=true)
     */
    private $propsPath;



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
}
