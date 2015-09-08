<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Content
 *
 * @ORM\Table(name="content", indexes={@ORM\Index(name="IDX_FEC530A95B8F2BDB", columns={"deposit_id"}), @ORM\Index(name="IDX_FEC530A9A3D201B3", columns={"au_id"})})
 * @ORM\Entity
 */
class Content
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=false)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @var integer
     *
     * @ORM\Column(name="size", type="integer", nullable=true)
     */
    private $size;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="date_deposited", type="datetime", nullable=false)
     */
    private $dateDeposited;

    /**
     * @var string
     *
     * @ORM\Column(name="checksum_type", type="string", length=24, nullable=true)
     */
    private $checksumType;

    /**
     * @var string
     *
     * @ORM\Column(name="checksum_value", type="string", length=255, nullable=true)
     */
    private $checksumValue;

    /**
     * @var boolean
     *
     * @ORM\Column(name="recrawl", type="boolean", nullable=false)
     */
    private $recrawl;

    /**
     * @var boolean
     *
     * @ORM\Column(name="verified_size", type="boolean", nullable=false)
     */
    private $verifiedSize;

    /**
     * @var Deposit
     *
     * @ORM\ManyToOne(targetEntity="Deposit", inversedBy="content")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="deposit_id", referencedColumnName="id")
     * })
     */
    private $deposit;

    /**
     * @var Au
     *
     * @ORM\ManyToOne(targetEntity="Au", inversedBy="content")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="au_id", referencedColumnName="id")
     * })
     */
    private $au;

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
    public function setDateDeposited($dateDeposited)
    {
        $this->dateDeposited = $dateDeposited;

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
     * @param boolean $recrawl
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
     * @return boolean 
     */
    public function getRecrawl()
    {
        return $this->recrawl;
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

    /**
     * Set deposit
     *
     * @param Deposit $deposit
     * @return Content
     */
    public function setDeposit(Deposit $deposit = null)
    {
        $this->deposit = $deposit;

        return $this;
    }

    /**
     * Get deposit
     *
     * @return Deposit
     */
    public function getDeposit()
    {
        return $this->deposit;
    }

    /**
     * Set au
     *
     * @param Au $au
     * @return Content
     */
    public function setAu(Au $au = null)
    {
        $this->au = $au;

        return $this;
    }

    /**
     * Get au
     *
     * @return Au
     */
    public function getAu()
    {
        return $this->au;
    }
}
