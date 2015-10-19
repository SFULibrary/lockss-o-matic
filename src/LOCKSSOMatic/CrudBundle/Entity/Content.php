<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Content that has been deposited to LOCKSSOMatic.
 *
 * @ORM\Table(name="content")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Content implements GetPlnInterface
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
     * The URL for the content.
     * 
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=false)
     */
    private $url;

    /**
     * The title of the content as deposited to LOCKSSOMatic.
     * 
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * The size of the content in 1000-byte units.
     *
     * @var integer
     *
     * @ORM\Column(name="size", type="integer", nullable=true)
     */
    private $size;

    /**
     * The date the deposit was recieved. Set automatically when the content
     * deposit is saved.
     *
     * @var DateTime
     *
     * @ORM\Column(name="date_deposited", type="datetime", nullable=false)
     */
    private $dateDeposited;

    /**
     * The checksum type for verifying the deposit. One of SHA1 or MD5.
     * @var string
     *
     * @ORM\Column(name="checksum_type", type="string", length=24, nullable=true)
     */
    private $checksumType;

    /**
     * The value of the checksum.
     *
     * TODO should this be uppercase?
     *
     * @var string
     *
     * @ORM\Column(name="checksum_value", type="string", length=255, nullable=true)
     */
    private $checksumValue;

    /**
     * True if the content should be recrawled.
     *
     * @var boolean
     *
     * @ORM\Column(name="recrawl", type="boolean", nullable=false)
     */
    private $recrawl;

    /**
     * The deposit that registered this content in the database.
     *
     * @var Deposit
     *
     * @ORM\ManyToOne(targetEntity="Deposit", inversedBy="content")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="deposit_id", referencedColumnName="id")
     * })
     */
    private $deposit;

    /**
     * The AU this content is a part of.
     *
     * @var Au
     *
     * @ORM\ManyToOne(targetEntity="Au", inversedBy="content")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="au_id", referencedColumnName="id")
     * })
     */
    private $au;

    /**
     * @ORM\OneToMany(targetEntity="ContentProperty", mappedBy="content")
     * @var ArrayCollection
     */
    private $contentProperties;

    public function __construct()
    {
        $this->contentProperties = new ArrayCollection();
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
     * Set deposit
     *
     * @param Deposit $deposit
     * @return Content
     */
    public function setDeposit(Deposit $deposit = null)
    {
        $this->deposit = $deposit;
        $deposit->addContent($this);
        
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
        $au->addContent($this);

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
    /**
     * @ORM\PrePersist
     */
    public function setDepositDate() {
        if($this->dateDeposited === null) {
            $this->dateDeposited = new DateTime();
        }
    }

    /**
     * Add contentProperties
     *
     * @param ContentProperty $contentProperties
     * @return Content
     */
    public function addContentProperty(ContentProperty $contentProperties)
    {
        $this->contentProperties[] = $contentProperties;

        return $this;
    }

    /**
     * Remove contentProperties
     *
     * @param ContentProperty $contentProperties
     */
    public function removeContentProperty(ContentProperty $contentProperties)
    {
        $this->contentProperties->removeElement($contentProperties);
    }

    public function hasContentProperty($key) {
        return $this->contentProperties->containsKey($key);
    }

    /**
     * Get contentProperties
     *
     * @return Collection
     */
    public function getContentProperties()
    {
        return $this->contentProperties;
    }

    public function getContentPropertyValue($key, $encoded = false) {
        $value = null;
        foreach($this->getContentProperties() as $prop) {
            if($prop->getPropertyKey() === $key) {
                $value = $prop->getPropertyValue();
                break;
            }
        }
        if($encoded === false || $value === null) {
            return $value;
        }
        $callback = function($matches) {
            $char = ord($matches[0]);
            return '%' . strtoupper(sprintf("%02x", $char));
        };
        return preg_replace_callback('/[^-_*a-zA-Z0-9]/', $callback, $value);
    }

    /**
     * Generate the AUid that this piece of content belongs in.
     *
     * @return string
     */
    public function generateAuid() {
        $plugin = $this->getDeposit()->getContentProvider()->getPlugin();
        if($plugin === null) {
            return null;
        }
        $pluginKey = str_replace('.', '|', $plugin->getPluginIdentifier());
        $auKey = '';
        $propNames = $plugin->getDefinitionalProperties();
        sort($propNames);

        foreach($propNames as $name) {
            $auKey .= '&' . $name . '~' . $this->getContentPropertyValue($name, true);
        }
        $this->auid = $pluginKey . $auKey;
        return $this->auid;
    }

    /**
     * {@inheritDoc}
     */
    public function getPln()
    {
        return $this->getDeposit()->getContentProvider()->getPln();
    }


}
