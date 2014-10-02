<?php

namespace LOCKSSOMatic\CRUDBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ContentProviders
 *
 * @ORM\Table(name="content_providers", indexes={@ORM\Index(name="content_owners_id_idx", columns={"content_owners_id"})})
 * @ORM\Entity
 */
class ContentProviders
{
    /**
    * Collection property required for many-to-one relationship with ContentOwners.
    * 
    * @ORM\ManyToOne(targetEntity="ContentOwners", inversedBy="contentProvider")
    * @ORM\JoinColumn(name="content_owners_id", referencedColumnName="id")
    */
    protected $contentOwner;

    /**
     * Property required for one-to-many relationship with Deposits.
     * 
     * @ORM\OneToMany(targetEntity="Deposits", mappedBy="ContentProvider")
     */
    protected $deposits;
    
    /**
     * Initializes the $collectionowner property.
     */
    public function __construct()
    {
        $this->deposits = new ArrayCollection();
    }

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
     * @ORM\Column(name="pln_id", type="integer", nullable=true)
     */
    private $plnId;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="text", nullable=true)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text", nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="ip_address", type="text", nullable=true)
     */
    private $ipAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="hostname", type="text", nullable=true)
     */
    private $hostname;

    /**
     * @var string
     *
     * @ORM\Column(name="checksum_type", type="text", nullable=true)
     */
    private $checksumType;

    /**
     * @var integer
     *
     * @ORM\Column(name="max_file_size", type="integer", nullable=true)
     */
    private $maxFileSize;

    /**
     * @var integer
     *
     * @ORM\Column(name="max_au_size", type="integer", nullable=true)
     */
    private $maxAuSize;

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
     * Set plnId
     *
     * @param integer $plnId
     * @return Plns
     */
    public function setPlnId($plnId)
    {
        $this->plnId = $plnId;

        return $this;
    }

    /**
     * Get plnId
     *
     * @return integer 
     */
    public function getPlnId()
    {
        return $this->plnId;
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
     * Get maxFileSize
     *
     * @return integer 
     */
    public function getMaxFileSize()
    {
        return $this->maxFileSize;
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
     * Get maxAuize
     *
     * @return integer 
     */
    public function getMaxAuSize()
    {
        return $this->maxAuSize;
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
     * @var \LOCKSSOMatic\CRUDBundle\Entity\Plns
     */
    private $pln;


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
