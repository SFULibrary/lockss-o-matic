<?php

namespace LOCKSSOMatic\CRUDBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Content
 *
 * @ORM\Table(name="content", indexes={@ORM\Index(name="content_providers_id_idx", columns={"content_providers_id"}), @ORM\Index(name="deposits_id_idx", columns={"deposits_id"})})
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
     * @var integer
     *
     * @ORM\Column(name="content_providers_id", type="integer", nullable=true)
     */
    private $contentProvidersId;

    /**
     * @var integer
     *
     * @ORM\Column(name="deposits_id", type="integer", nullable=true)
     */
    private $depositsId;

    /**
     * @var integer
     *
     * @ORM\Column(name="aus_id", type="integer", nullable=true)
     */
    private $ausId;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="text", nullable=true)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="text", nullable=true)
     */
    private $title;

    /**
     * @var integer
     *
     * @ORM\Column(name="size", type="integer", nullable=true)
     */
    private $size;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_added_to_au", type="datetime", nullable=true)
     */
    private $dateAddedToAu;

    /**
     * @var string
     *
     * @ORM\Column(name="checksum_type", type="text", nullable=true)
     */
    private $checksumType;

    /**
     * @var string
     *
     * @ORM\Column(name="checksum_value", type="text", nullable=true)
     */
    private $checksumValue;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="reharvest", type="boolean", nullable=false)
     */
    private $reharvest;



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
     * Set contentProvidersId
     *
     * @param integer $contentProvidersId
     * @return Content
     */
    public function setContentProvidersId($contentProvidersId)
    {
        $this->contentProvidersId = $contentProvidersId;

        return $this;
    }

    /**
     * Get contentProvidersId
     *
     * @return integer 
     */
    public function getContentProvidersId()
    {
        return $this->contentProvidersId;
    }

    /**
     * Set depositsId
     *
     * @param integer $depositsId
     * @return Content
     */
    public function setDepositsId($depositsId)
    {
        $this->depositsId = $depositsId;

        return $this;
    }

    /**
     * Get depositsId
     *
     * @return integer 
     */
    public function getDepositsId()
    {
        return $this->depositsId;
    }

    /**
     * Set ausId
     *
     * @param integer $ausId
     * @return Content
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
     * Set dateAddedToAu
     *
     * @param \DateTime $dateAddedToAu
     * @return Content
     */
    public function setDateAddedToAu($dateAddedToAu)
    {
        $this->dateAddedToAu = $dateAddedToAu;

        return $this;
    }

    /**
     * Get dateAddedToAu
     *
     * @return \DateTime 
     */
    public function getDateAddedToAu()
    {
        return $this->dateAddedToAu;
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
     * Set reharvest
     *
     * @param \boolean $reharvest
     * @return Content
     */
    public function setReharvest(\boolean $reharvest)
    {
        $this->reharvest = $reharvest;

        return $this;
    }

    /**
     * Get reharvest
     *
     * @return \boolean
     */
    public function getReharvest()
    {
        return $this->reharvest;
    }
}
