<?php

namespace LOCKSSOMatic\CRUDBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @var \DateTime
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
     * @var \LOCKSSOMatic\CRUDBundle\Entity\Deposits
     */
    private $deposit;

    /**
     * @var \LOCKSSOMatic\CRUDBundle\Entity\Aus
     */
    private $au;

    /**
     * Stringify the entity
     * 
     * @return string
     */
    public function __toString() {
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
     * @param \DateTime $dateDeposited
     * @return Content
     */
    public function setDateDeposited($dateDeposited)
    {
        $this->dateDeposited = $dateDeposited;

        return $this;
    }

    /**
     * Get dateDeposited
     *
     * @return \DateTime 
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
     * @param \LOCKSSOMatic\CRUDBundle\Entity\Deposits $deposit
     * @return Content
     */
    public function setDeposit(\LOCKSSOMatic\CRUDBundle\Entity\Deposits $deposit = null)
    {
        $this->deposit = $deposit;

        return $this;
    }

    /**
     * Get deposit
     *
     * @return \LOCKSSOMatic\CRUDBundle\Entity\Deposits 
     */
    public function getDeposit()
    {
        return $this->deposit;
    }

    /**
     * Set au
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\Aus $au
     * @return Content
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
}
