<?php

namespace LOCKSSOMatic\CRUDBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * AuProperties
 */
class AuProperties
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $ausId;

    /**
     * @var integer
     */
    private $parentId;

    /**
     * @var string
     */
    private $propertyKey;

    /**
     * @var string
     */
    private $propertyValue;

    /**
     * @var Collection
     */
    private $children;

    /**
     * @var Aus
     */
    private $au;

    /**
     * @var AuProperties
     */
    private $parent;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    /**
     * Stringify the entity
     *
     * @return string
     */
    public function __toString()
    {
        return $this->propertyKey;
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
     * Set ausId
     *
     * @param integer $ausId
     * @return AuProperties
     */
    public function setAusId($ausId)
    {
        $this->ausId = $ausId;

        return $this;
    }

    /**
     * Get ausId
     *
     * @return integer
     */
    public function getAusId()
    {
        return $this->ausId;
    }

    /**
     * Set parentId
     *
     * @param integer $parentId
     * @return AuProperties
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * Get parentId
     *
     * @return integer
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Set propertyKey
     *
     * @param string $propertyKey
     * @return AuProperties
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
     * @return AuProperties
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
     * Add children
     *
     * @param AuProperties $children
     * @return AuProperties
     */
    public function addChild(AuProperties $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param AuProperties $children
     */
    public function removeChild(AuProperties $children)
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
     * Set au
     *
     * @param Aus $au
     * @return AuProperties
     */
    public function setAu(Aus $au = null)
    {
        $this->au = $au;

        return $this;
    }

    /**
     * Get au
     *
     * @return Aus
     */
    public function getAu()
    {
        return $this->au;
    }

    /**
     * Set parent
     *
     * @param AuProperties $parent
     * @return AuProperties
     */
    public function setParent(AuProperties $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return AuProperties
     */
    public function getParent()
    {
        return $this->parent;
    }
}
