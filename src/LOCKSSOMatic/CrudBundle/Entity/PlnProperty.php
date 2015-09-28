<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Pln Properties are hierarchial.
 *
 * @ORM\Table(name="pln_properties")
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
     * The name of the property.
     *
     * @var string
     *
     * @ORM\Column(name="property_key", type="string", length=255, nullable=false)
     */
    private $propertyKey;

    /**
     * The value of the property. Parent properties don't have values. The value
     * may be an array.
     *
     * @var string|array
     *
     * @ORM\Column(name="property_value", type="text", nullable=true)
     */
    private $propertyValue;

    /**
     * True if the property value is a list/array.
     * @var boolean
     *
     * @ORM\Column(name="is_list", type="boolean", nullable=false)
     */
    private $isList;

    /**
     * The parent of this property.
     * 
     * @var PlnProperty
     *
     * @ORM\ManyToOne(targetEntity="PlnProperty", inversedBy="children")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $parent;

    /**
     * The PLN for the property
     * 
     * @var Pln
     *
     * @ORM\ManyToOne(targetEntity="Pln", inversedBy="plnProperties")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pln_id", referencedColumnName="id")
     * })
     */
    private $pln;

    /**
     * The children of the property.
     * 
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
     * @param string|array $propertyValue
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

    /**
     * Return true if the value of the property is a list.
     *
     * @return boolean
     */
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

    /**
     * Return true if the property has a parent.
     *
     * @return boolean
     */
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

    /**
     * Return true if the property has children.
     *
     * @return boolean
     */
    public function hasChildren() {
        return $this->children->count() > 0;
    }
}
