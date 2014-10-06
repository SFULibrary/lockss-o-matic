<?php

namespace LOCKSSOMatic\CRUDBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PlnProperties
 */
class PlnProperties
{
    /**
     * @var integer
     */
    private $id;

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
     * @var \LOCKSSOMatic\CRUDBundle\Entity\Plns
     */
    private $pln;

    /**
     * @var \LOCKSSOMatic\CRUDBundle\Entity\PlnProperties
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
     * Set propertyKey
     *
     * @param string $propertyKey
     * @return PlnProperties
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
     * @return PlnProperties
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
     * @param \LOCKSSOMatic\CRUDBundle\Entity\PlnProperties $children
     * @return PlnProperties
     */
    public function addChild(\LOCKSSOMatic\CRUDBundle\Entity\PlnProperties $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\PlnProperties $children
     */
    public function removeChild(\LOCKSSOMatic\CRUDBundle\Entity\PlnProperties $children)
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
     * Set pln
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\Plns $pln
     * @return PlnProperties
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
     * Set parent
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\PlnProperties $parent
     * @return PlnProperties
     */
    public function setParent(\LOCKSSOMatic\CRUDBundle\Entity\PlnProperties $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \LOCKSSOMatic\CRUDBundle\Entity\PlnProperties 
     */
    public function getParent()
    {
        return $this->parent;
    }
}
