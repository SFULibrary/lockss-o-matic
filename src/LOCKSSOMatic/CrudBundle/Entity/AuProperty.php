<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * AUs have hierarchial properties.
 *
 * @ORM\Table(name="au_properties")
 * @ORM\Entity
 */
class AuProperty implements GetPlnInterface
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
     * The name of the property, corresponding to the name attribute in XML.
     *
     * @var string
     * @ORM\Column(name="property_key", type="string", length=255, nullable=false)
     */
    private $propertyKey;

    /**
     * The value of the property, if the property has a value. Properties with
     * child properties don't have values.
     *
     * @var string|array
     * @ORM\Column(name="property_value", type="text", nullable=true)
     */
    private $propertyValue;

    /**
     * The parent of the property, if it has one. 
     *
     * @var AuProperty
     *
     * @ORM\ManyToOne(targetEntity="AuProperty", inversedBy="children")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     * })
     */
    private $parent;

    /**
     * The AU for the property.
     *
     * @var Au
     *
     * @ORM\ManyToOne(targetEntity="Au", inversedBy="auProperties")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="au_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $au;

    /**
     * The children of the property.
     *
     * @ORM\OneToMany(targetEntity="AuProperty", mappedBy="parent")
     * @var ArrayCollection
     */
    private $children;

    public function __construct()
    {
        $this->children = new ArrayCollection();
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
     * @return AuProperty
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
     * @param string $propertyValue
     * @return AuProperty
     */
    public function setPropertyValue($propertyValue)
    {
        $this->propertyValue = $propertyValue;

        return $this;
    }

    /**
     * Get propertyValue
     *
     * @return string 
     */
    public function getPropertyValue()
    {
        return $this->propertyValue;
    }

    /**
     * Set parent
     *
     * @param AuProperty $parent
     * @return AuProperty
     */
    public function setParent(AuProperty $parent = null)
    {
        $this->parent = $parent;
        if ($parent !== null) {
            $parent->addChild($this);
        }
        return $this;
    }

    /**
     * Get parent
     *
     * @return AuProperty
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Returns true if the property has a parent.
     *
     * @return boolean
     */
    public function hasParent()
    {
        return $this->parent !== null;
    }

    /**
     * Set au
     *
     * @param Au $au
     * @return AuProperty
     */
    public function setAu(Au $au = null)
    {
        $this->au = $au;
        $au->addAuProperty($this);

        return $this;
    }

    /**
     * Get au
     *
     * @return Au
     */
    public function getAu()
    {
        return $this->au;
    }

    /**
     * Add children
     *
     * @param AuProperty $children
     * @return AuProperty
     */
    public function addChild(AuProperty $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param AuProperty $children
     */
    public function removeChild(AuProperty $children)
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
    public function hasChildren()
    {
        return count($this->children) > 0;
    }

    /**
     * {@inheritDoc}
     */
    public function getPln()
    {
        return $this->getAu()->getPln();
    }

}
