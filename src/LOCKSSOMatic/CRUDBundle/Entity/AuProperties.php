<?php

/* 
 * The MIT License
 *
 * Copyright (c) 2014 Mark Jordan, mjordan@sfu.ca.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace LOCKSSOMatic\CRUDBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use LOCKSSOMatic\CRUDBundle\Entity\AuProperties;
use LOCKSSOMatic\CRUDBundle\Entity\Aus;

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
     * Set propertyKey
     *
     * @param string $propertyKey
     * 
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
     * 
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
     * 
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
     * @return string
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
     * @return string
     * 
     * @return Aus 
     */
    public function getAu()
    {
        return $this->au;
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

}
