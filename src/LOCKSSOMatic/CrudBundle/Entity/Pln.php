<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Pln
 *
 * @ORM\Table(name="plns")
 * @ORM\Entity
 *
 * TODO: Add plugin.registries (list of URLs)
 * TODO: add plugin.titleDbs
 */
class Pln
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
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="propServer", type="string", length=255, nullable=false)
     */
    private $propserver;

    /**
     * @var string
     *
     * @ORM\Column(name="props_path", type="text", nullable=true)
     */
    private $propsPath;

    /**
     * @ORM\OneToMany(targetEntity="Au", mappedBy="pln")
     * @var ArrayCollection
     */
    private $aus;

    /**
     * @ORM\OneToMany(targetEntity="Box", mappedBy="pln");
     * @var ArrayCollection
     */
    private $boxes;

    /**
     * @ORM\OneToMany(targetEntity="ExternalTitleDb", mappedBy="pln")
     * @var ArrayCollection
     */
    private $externalTitleDbs;

    /**
     * @ORM\OneToMany(targetEntity="PlnProperty", mappedBy="pln");
     * @var ArrayCollection
     */
    private $plnProperties;

    public function __construct() {
        $this->aus = new ArrayCollection();
        $this->boxes = new ArrayCollection();
        $this->externalTitleDbs = new ArrayCollection();
        $this->plnProperties = new ArrayCollection();
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
     * @return Pln
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
     * Set propserver
     *
     * @param string $propserver
     * @return Pln
     */
    public function setPropserver($propserver)
    {
        $this->propserver = $propserver;

        return $this;
    }

    /**
     * Get propserver
     *
     * @return string 
     */
    public function getPropserver()
    {
        return $this->propserver;
    }

    /**
     * Set propsPath
     *
     * @param string $propsPath
     * @return Pln
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

    public function __toString() {
        return $this->name;
    }

    /**
     * Add aus
     *
     * @param Au $aus
     * @return Pln
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
     * Add boxes
     *
     * @param Box $boxes
     * @return Pln
     */
    public function addBox(Box $boxes)
    {
        $this->boxes[] = $boxes;

        return $this;
    }

    /**
     * Remove boxes
     *
     * @param Box $boxes
     */
    public function removeBox(Box $boxes)
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
     * Add externalTitleDbs
     *
     * @param ExternalTitleDb $externalTitleDbs
     * @return Pln
     */
    public function addExternalTitleDb(ExternalTitleDb $externalTitleDbs)
    {
        $this->externalTitleDbs[] = $externalTitleDbs;

        return $this;
    }

    /**
     * Remove externalTitleDbs
     *
     * @param ExternalTitleDb $externalTitleDbs
     */
    public function removeExternalTitleDb(ExternalTitleDb $externalTitleDbs)
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
     * Add plnProperties
     *
     * @param PlnProperty $plnProperties
     * @return Pln
     */
    public function addPlnProperty(PlnProperty $plnProperties)
    {
        $this->plnProperties[] = $plnProperties;

        return $this;
    }

    /**
     * Remove plnProperties
     *
     * @param PlnProperty $plnProperties
     */
    public function removePlnProperty(PlnProperty $plnProperties)
    {
        $this->plnProperties->removeElement($plnProperties);
    }

    /**
     * Get plnProperties
     *
     * @return PlnProperty[]
     */
    public function getPlnProperties()
    {
        return $this->plnProperties;
    }

    public function getRootPluginProperties() {
        $properties = array();
        foreach($this->plnProperties as $p) {
            if($p->hasParent()) {
                continue;
            }
            $properties[] = $p;
        }
        return $properties;
    }

    public function getProperty($name) {
        $props = $this->getPlnProperties();

        foreach($this->getPlnProperties() as $prop) {
            if($prop->getPropertyKey() === $name) {
                return $prop;
            }
        }
        die("cannot find {$name} in " . count($props));
        return null;
    }
}
