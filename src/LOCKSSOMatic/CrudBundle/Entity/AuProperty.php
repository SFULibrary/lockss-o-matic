<?php

/*
 * The MIT License
 *
 * Copyright 2014-2016. Michael Joyce <ubermichael@gmail.com>.
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
     * @var int
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
     *   @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
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
     *   @ORM\JoinColumn(name="au_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $au;

    /**
     * The children of the property.
     *
     * @ORM\OneToMany(targetEntity="AuProperty", mappedBy="parent")
     *
     * @var ArrayCollection
     */
    private $children;

    /**
     * Construct an AU property.
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set propertyKey.
     *
     * @param string $propertyKey
     *
     * @return AuProperty
     */
    public function setPropertyKey($propertyKey)
    {
        $this->propertyKey = $propertyKey;

        return $this;
    }

    /**
     * Get propertyKey.
     *
     * @return string
     */
    public function getPropertyKey()
    {
        return $this->propertyKey;
    }

    /**
     * Set propertyValue.
     *
     * @param string $propertyValue
     *
     * @return AuProperty
     */
    public function setPropertyValue($propertyValue)
    {
        $this->propertyValue = $propertyValue;

        return $this;
    }

    /**
     * Get propertyValue.
     *
     * @return string
     */
    public function getPropertyValue()
    {
        return $this->propertyValue;
    }

    /**
     * Set parent.
     *
     * @param AuProperty $parent
     *
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
     * Get parent.
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
     * @return bool
     */
    public function hasParent()
    {
        return $this->parent !== null;
    }

    /**
     * Set au.
     *
     * @param Au $au
     *
     * @return AuProperty
     */
    public function setAu(Au $au = null)
    {
        $this->au = $au;
        $au->addAuProperty($this);

        return $this;
    }

    /**
     * Get au.
     *
     * @return Au
     */
    public function getAu()
    {
        return $this->au;
    }

    /**
     * Add children.
     *
     * @param AuProperty $children
     *
     * @return AuProperty
     */
    public function addChild(AuProperty $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children.
     *
     * @param AuProperty $children
     */
    public function removeChild(AuProperty $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children.
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
     * @return bool
     */
    public function hasChildren()
    {
        return count($this->children) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getPln()
    {
        return $this->getAu()->getPln();
    }
}
