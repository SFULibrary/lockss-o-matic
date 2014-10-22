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

use DateTime;

/**
 * BoxStatus
 */
class BoxStatus
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var DateTime
     */
    private $queryDate;

    /**
     * @var string
     */
    private $propertyKey;

    /**
     * @var string
     */
    private $propertyValue;

    /**
     * @var Boxes
     */
    private $box;


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
     * Set queryDate
     *
     * @return BoxStatus
     */
    public function setQueryDate()
    {
        $this->queryDate = new DateTime();

        return $this;
    }

    /**
     * Get queryDate
     *
     * @return DateTime
     */
    public function getQueryDate()
    {
        return $this->queryDate;
    }

    /**
     * Set propertyKey
     *
     * @param string $propertyKey
     * @return BoxStatus
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
     * @return BoxStatus
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
     * Set box
     *
     * @param Boxes $box
     * @return BoxStatus
     */
    public function setBox(Boxes $box = null)
    {
        $this->box = $box;

        return $this;
    }

    /**
     * Get box
     *
     * @return Boxes
     */
    public function getBox()
    {
        return $this->box;
    }
}
