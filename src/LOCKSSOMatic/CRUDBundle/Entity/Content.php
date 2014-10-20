<?php

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
    private $verifiedFileSize;

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
     * Set verifiedFileSize
     *
     * @param boolean $verifiedFileSize
     * @return Content
     */
    public function setVerifiedFileSize($verifiedFileSize)
    {
        $this->verifiedFileSize = $verifiedFileSize;

        return $this;
    }

    /**
     * Get verifiedFileSize
     *
     * @return boolean 
     */
    public function getVerifiedFileSize()
    {
        return $this->verifiedFileSize;
    }
}
