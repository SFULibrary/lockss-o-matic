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
 * Content
 */
class Content
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $title;

    /**
     * @var integer
     */
    private $size;

    /**
     * @var DateTime
     */
    private $dateDeposited;

    /**
     * @var string
     */
    private $checksumType;

    /**
     * @var string
     */
    private $checksumValue;

    /**
     * @var integer
     */
    private $recrawl;

    /**
     * @var Deposits
     */
    private $deposit;

    /**
     * @var Aus
     */
    private $au;

    /**
     * @var boolean
     */
    private $verifiedSize;

    public function __construct() {
        $this->verifiedSize = false;
    }
    
    /**
     * Stringify the entity
     *
     * @return string
     */
    public function __toString()
    {
        return $this->title;
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
     * Set url
     *
     * @param string $url
     * @return Content
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Content
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set size
     *
     * @param integer $size
     * @return Content
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return integer
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set dateDeposited
     *
     * @param DateTime $dateDeposited
     * @return Content
     */
    public function setDateDeposited()
    {
        $this->dateDeposited = new DateTime();

        return $this;
    }

    /**
     * Get dateDeposited
     *
     * @return DateTime
     */
    public function getDateDeposited()
    {
        return $this->dateDeposited;
    }

    /**
     * Set checksumType
     *
     * @param string $checksumType
     * @return Content
     */
    public function setChecksumType($checksumType)
    {
        $this->checksumType = $checksumType;

        return $this;
    }

    /**
     * Get checksumType
     *
     * @return string
     */
    public function getChecksumType()
    {
        return $this->checksumType;
    }

    /**
     * Set checksumValue
     *
     * @param string $checksumValue
     * @return Content
     */
    public function setChecksumValue($checksumValue)
    {
        $this->checksumValue = $checksumValue;

        return $this;
    }

    /**
     * Get checksumValue
     *
     * @return string
     */
    public function getChecksumValue()
    {
        return $this->checksumValue;
    }

    /**
     * Set recrawl
     *
     * @param integer $recrawl
     * @return Content
     */
    public function setRecrawl($recrawl)
    {
        $this->recrawl = $recrawl;

        return $this;
    }

    /**
     * Get recrawl
     *
     * @return integer
     */
    public function getRecrawl()
    {
        return $this->recrawl;
    }

    /**
     * Set deposit
     *
     * @param Deposits $deposit
     * @return Content
     */
    public function setDeposit(Deposits $deposit = null)
    {
        $this->deposit = $deposit;

        return $this;
    }

    /**
     * Get deposit
     *
     * @return Deposits
     */
    public function getDeposit()
    {
        return $this->deposit;
    }

    /**
     * Set au
     *
     * @param Aus $au
     * @return Content
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
     * Set verifiedSize
     *
     * @param boolean $verifiedSize
     * @return Content
     */
    public function setVerifiedSize($verifiedSize)
    {
        $this->verifiedSize = $verifiedSize;

        return $this;
    }

    /**
     * Get verifiedSize
     *
     * @return boolean 
     */
    public function getVerifiedSize()
    {
        return $this->verifiedSize;
    }
}
