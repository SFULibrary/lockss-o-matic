<?php

namespace LOCKSSOMatic\CRUDBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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
     * @var Collection
     */
    private $deposits;

    /**
     * @var ContentOwners
     */
    private $contentOwner;

    /**
     * @var Plns
     */
    private $pln;

    /**
     * @var string
     */
    private $uuid;

    /**
     * @var string
     */
    private $permissionUrl;

    /**
     * @var Plugins
     */
    private $plugin;

    /**
     * @var Collection
     */
    private $aus;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->deposits = new ArrayCollection();
    }

    /**
     * Stringify the entity
     *
     * @return string
     */
    public function __toString()
    {
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
     * @param Deposits $deposits
     * @return ContentProviders
     */
    public function addDeposit(Deposits $deposits)
    {
        $this->deposits[] = $deposits;

        return $this;
    }

    /**
     * Remove deposits
     *
     * @param Deposits $deposits
     */
    public function removeDeposit(Deposits $deposits)
    {
        $this->deposits->removeElement($deposits);
    }

    /**
     * Get deposits
     *
     * @return Collection
     */
    public function getDeposits()
    {
        return $this->deposits;
    }

    /**
     * Set contentOwner
     *
     * @param ContentOwners $contentOwner
     * @return ContentProviders
     */
    public function setContentOwner(ContentOwners $contentOwner = null)
    {
        $this->contentOwner = $contentOwner;

        return $this;
    }

    /**
     * Get contentOwner
     *
     * @return ContentOwners
     */
    public function getContentOwner()
    {
        return $this->contentOwner;
    }

    /**
     * Set pln
     *
     * @param Plns $pln
     * @return ContentProviders
     */
    public function setPln(Plns $pln = null)
    {
        $this->pln = $pln;

        return $this;
    }

    /**
     * Get pln
     *
     * @return Plns
     */
    public function getPln()
    {
        return $this->pln;
    }

    /**
     * Set uuid
     *
     * @param string $uuid
     * @return ContentProviders
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }
    
    /**
     * Generate a UUID for the provider if it does not have one.
     * 
     * Called automatically by doctrine before creating a record for the entity
     * in the database.
     */
    public function generateUuid() {
        if($this->uuid === null) {
            $this->uuid = \J20\Uuid\Uuid::v4();
        }
    }

    /**
     * Get uuid
     *
     * @return string 
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set permissionUrl
     *
     * @param string $permissionUrl
     * @return ContentProviders
     */
    public function setPermissionUrl($permissionUrl)
    {
        $this->permissionUrl = $permissionUrl;

        return $this;
    }

    /**
     * Get permissionUrl
     *
     * @return string 
     */
    public function getPermissionUrl()
    {
        return $this->permissionUrl;
    }

    /**
     * Set plugin
     *
     * @param Plugins $plugin
     * @return ContentProviders
     */
    public function setPlugin(Plugins $plugin = null)
    {
        $this->plugin = $plugin;

        return $this;
    }

    /**
     * Get plugin
     *
     * @return Plugins 
     */
    public function getPlugin()
    {
        return $this->plugin;
    }

    /**
     * Add aus
     *
     * @param Aus $aus
     * @return ContentProviders
     */
    public function addAus(Aus $aus)
    {
        $this->aus[] = $aus;

        return $this;
    }

    /**
     * Remove aus
     *
     * @param Aus $aus
     */
    public function removeAus(Aus $aus)
    {
        $this->aus->removeElement($aus);
    }

    /**
     * Get aus
     *
     * @return Collection 
     */
    public function getAus()
    {
        return $this->aus;
    }

    /**
     * Get the open AU for the content provider, or create a new one
     * if necessary.
     * 
     * @return Aus
     */
    public function getOpenAu()
    {
        $callback = function(Aus $au) {
            return $au->getOpen() === true;
        };
        
        $aus = $this->getAus()->filter($callback);
        if($aus->count() === 1) {
            return $aus->last();
        }
        if ($aus->count() > 1) {
            // this is an error.
            return $aus->last();
        }

        $au = new Aus();
        $au->setContentProvider($this);
        return $au;
    }

}
