<?php

namespace LOCKSSOMatic\CRUDBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ContentProviders
 */
class ContentProviders
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $ipAddress;

    /**
     * @var string
     */
    private $hostname;

    /**
     * @var string
     */
    private $checksumType;

    /**
     * @var integer
     */
    private $maxFileSize;

    /**
     * @var integer
     */
    private $maxAuSize;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $deposits;

    /**
     * @var \LOCKSSOMatic\CRUDBundle\Entity\ContentOwners
     */
    private $contentOwner;

    /**
     * @var \LOCKSSOMatic\CRUDBundle\Entity\Plns
     */
    private $pln;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->deposits = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Stringify the entity
     * 
     * @return string
     */
    public function __toString() {
        return $this->name;
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
     * Set type
     *
     * @param string $type
     * @return ContentProviders
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return ContentProviders
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set ipAddress
     *
     * @param string $ipAddress
     * @return ContentProviders
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    /**
     * Get ipAddress
     *
     * @return string 
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * Set hostname
     *
     * @param string $hostname
     * @return ContentProviders
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;

        return $this;
    }

    /**
     * Get hostname
     *
     * @return string 
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * Set checksumType
     *
     * @param string $checksumType
     * @return ContentProviders
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
     * Set maxFileSize
     *
     * @param integer $maxFileSize
     * @return ContentProviders
     */
    public function setMaxFileSize($maxFileSize)
    {
        $this->maxFileSize = $maxFileSize;

        return $this;
    }

    /**
     * Get maxFileSize
     *
     * @return integer 
     */
    public function getMaxFileSize()
    {
        return $this->maxFileSize;
    }

    /**
     * Set maxAuSize
     *
     * @param integer $maxAuSize
     * @return ContentProviders
     */
    public function setMaxAuSize($maxAuSize)
    {
        $this->maxAuSize = $maxAuSize;

        return $this;
    }

    /**
     * Get maxAuSize
     *
     * @return integer 
     */
    public function getMaxAuSize()
    {
        return $this->maxAuSize;
    }

    /**
     * Add deposits
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\Deposits $deposits
     * @return ContentProviders
     */
    public function addDeposit(\LOCKSSOMatic\CRUDBundle\Entity\Deposits $deposits)
    {
        $this->deposits[] = $deposits;

        return $this;
    }

    /**
     * Remove deposits
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\Deposits $deposits
     */
    public function removeDeposit(\LOCKSSOMatic\CRUDBundle\Entity\Deposits $deposits)
    {
        $this->deposits->removeElement($deposits);
    }

    /**
     * Get deposits
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDeposits()
    {
        return $this->deposits;
    }

    /**
     * Set contentOwner
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\ContentOwners $contentOwner
     * @return ContentProviders
     */
    public function setContentOwner(\LOCKSSOMatic\CRUDBundle\Entity\ContentOwners $contentOwner = null)
    {
        $this->contentOwner = $contentOwner;

        return $this;
    }

    /**
     * Get contentOwner
     *
     * @return \LOCKSSOMatic\CRUDBundle\Entity\ContentOwners 
     */
    public function getContentOwner()
    {
        return $this->contentOwner;
    }

    /**
     * Set pln
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\Plns $pln
     * @return ContentProviders
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
}
