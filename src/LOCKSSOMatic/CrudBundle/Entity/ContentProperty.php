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

use Doctrine\ORM\Mapping as ORM;

/**
 * Content Properties are hierarchial.
 * 
 * @todo can this be a serialized array? Can it really be that simple?
 *
 * @ORM\Table(name="content_properties")
 * @ORM\Entity
 */
class ContentProperty implements GetPlnInterface
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
     *
     * @var bool
     *
     * @ORM\Column(name="is_list", type="boolean", nullable=false)
     */
    private $isList;

    /**
     * The Content for the property.
     *
     * @var Content
     *
     * @ORM\ManyToOne(targetEntity="Content", inversedBy="contentProperties")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="content_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $content;

    /**
     * Build a new content property.
     */
    public function __construct()
    {
        $this->isList = false;
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
     * @return PlnProperty
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
     * @param string|array $propertyValue
     *
     * @return PlnProperty
     */
    public function setPropertyValue($propertyValue)
    {
        if (is_array($propertyValue)) {
            $this->isList = true;
            $this->propertyValue = serialize($propertyValue);
        } else {
            $this->isList = false;
            $this->propertyValue = $propertyValue;
        }

        return $this;
    }

    /**
     * Get propertyValue. Returns an array or a string.
     *
     * @return array|string
     */
    public function getPropertyValue()
    {
        if ($this->isList) {
            return unserialize($this->propertyValue);
        }

        return $this->propertyValue;
    }

    /**
     * Return true if the value of the property is a list.
     *
     * @return bool
     */
    public function isList()
    {
        return $this->isList;
    }

    /**
     * Set content.
     *
     * @param Content $content
     *
     * @return ContentProperty
     */
    public function setContent(Content $content = null)
    {
        $this->content = $content;
        $content->addContentProperty($this);

        return $this;
    }

    /**
     * Get content.
     *
     * @return Content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * {@inheritdoc}
     */
    public function getPln()
    {
        return $this->getContent()->getPln();
    }

}
