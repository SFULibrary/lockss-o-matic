<?php

namespace LOCKSSOMatic\CRUDBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $children;

    /**
     * @var \LOCKSSOMatic\CRUDBundle\Entity\Aus
     */
    private $au;

    /**
     * @var \LOCKSSOMatic\CRUDBundle\Entity\AuProperties
     */
    private $parent;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Stringify the entity
     * 
     * @return string
     */
    public function __toString() {
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
     * @param \LOCKSSOMatic\CRUDBundle\Entity\AuProperties $children
     * @return AuProperties
     */
    public function addChild(\LOCKSSOMatic\CRUDBundle\Entity\AuProperties $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\AuProperties $children
     */
    public function removeChild(\LOCKSSOMatic\CRUDBundle\Entity\AuProperties $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set au
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\Aus $au
     * @return AuProperties
     */
    public function setAu(\LOCKSSOMatic\CRUDBundle\Entity\Aus $au = null)
    {
        $this->au = $au;

        return $this;
    }

    /**
     * Get au
     *
     * @return \LOCKSSOMatic\CRUDBundle\Entity\Aus 
     */
    public function getAu()
    {
        return $this->au;
    }

    /**
     * Set parent
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\AuProperties $parent
     * @return AuProperties
     */
    public function setParent(\LOCKSSOMatic\CRUDBundle\Entity\AuProperties $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \LOCKSSOMatic\CRUDBundle\Entity\AuProperties 
     */
    public function getParent()
    {
        return $this->parent;
    }
}
