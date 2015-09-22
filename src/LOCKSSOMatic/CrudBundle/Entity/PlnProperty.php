<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * PlnProperty
 *
 * @ORM\Table(name="pln_properties", indexes={@ORM\Index(name="IDX_E1F16662C8BA1A08", columns={"pln_id"}), @ORM\Index(name="IDX_E1F16662727ACA70", columns={"parent_id"})})
 * @ORM\Entity
 */
class PlnProperty
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
     * @ORM\Column(name="property_key", type="string", length=255, nullable=false)
     */
    private $propertyKey;

    /**
     * @var string
     *
     * @ORM\Column(name="property_value", type="text", nullable=true)
     */
    private $propertyValue;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_list", type="boolean", nullable=false)
     */
    private $isList;

    /**
     * @var PlnProperty
     *
     * @ORM\ManyToOne(targetEntity="PlnProperty", inversedBy="children")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $parent;

    /**
     * @var Pln
     *
     * @ORM\ManyToOne(targetEntity="Pln", inversedBy="plnProperties")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pln_id", referencedColumnName="id")
     * })
     */
    private $pln;

    /**
     * @ORM\OneToMany(targetEntity="PlnProperty", mappedBy="parent")
     * @var ArrayCollection
     */
    private $children;

    public function __construct() {
        $this->children = new ArrayCollection();
        $this->isList = false;
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
     * Set propertyKey
     *
     * @param string $propertyKey
     * @return PlnProperty
     */
    public function setPropertyKey($propertyKey)
    {
        $this->propertyKey = $propertyKey;

        return $this;
    }

    /**
     * Get propertyKey
     *
     * @return string 
     */
    public function getPropertyKey()
    {
        return $this->propertyKey;
    }

    /**
     * Set propertyValue
     *
     * @param mixed $propertyValue
     * @return PlnProperty
     */
    public function setPropertyValue($propertyValue)
    {
        if(is_array($propertyValue)) {
            $this->isList = true;
            $this->propertyValue = serialize($propertyValue);
        } else {
            $this->isList = false;
            $this->propertyValue = $propertyValue;
        }

        return $this;
    }

    /**
     * Get propertyValue
     *
     * @return mixed
     */
    public function getPropertyValue()
    {
        if($this->isList) {
            return unserialize($this->propertyValue);
        }
        return $this->propertyValue;
    }

    public function isList() {
        return $this->isList;
    }

    /**
     * Set parent
     *
     * @param PlnProperty $parent
     * @return PlnProperty
     */
    public function setParent(PlnProperty $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return PlnProperty
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function hasParent() {
        return $this->parent !== null;
    }

    /**
     * Set pln
     *
     * @param Pln $pln
     * @return PlnProperty
     */
    public function setPln(Pln $pln = null)
    {
        $this->pln = $pln;

        return $this;
    }

    /**
     * Get pln
     *
     * @return Pln
     */
    public function getPln()
    {
        return $this->pln;
    }

    /**
     * Add children
     *
     * @param PlnProperty $children
     * @return PlnProperty
     */
    public function addChild(PlnProperty $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param PlnProperty $children
     */
    public function removeChild(PlnProperty $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    public function hasChildren() {
        return $this->children->count() > 0;
    }
}
