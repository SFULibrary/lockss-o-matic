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
 * @ORM\Entity(repositoryClass="ContentRepository")
 */
class Content implements GetPlnInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * The URL for the content.
     *
     * @todo is 255 long enough?
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
     * @var int
     *
     * @ORM\Column(name="size", type="bigint", nullable=true)
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
     *
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
     * @todo is this used anywhere?
     *
     * @var bool
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
     *   @ORM\JoinColumn(name="deposit_id", referencedColumnName="id", onDelete="CASCADE")
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
     *   @ORM\JoinColumn(name="au_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $au;

    /**
     * The contentProperties associated with this content.
     *
     * @ORM\OneToMany(targetEntity="ContentProperty", mappedBy="content")
     *
     * @var ArrayCollection
     */
    private $contentProperties;

    /**
     * Build a new Content item.
     */
    public function __construct() {
        $this->contentProperties = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set url.
     *
     * @param string $url
     *
     * @return Content
     */
    public function setUrl($url) {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url.
     *
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return Content
     */
    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Set size.
     *
     * @param int $size
     *
     * @return Content
     */
    public function setSize($size) {
        if ($size !== '') {
            $this->size = $size;
        }

        return $this;
    }

    /**
     * Get size.
     *
     * @return int
     */
    public function getSize() {
        return $this->size;
    }

    /**
     * Set dateDeposited.
     *
     * @param DateTime $dateDeposited
     *
     * @return Content
     */
    public function setDateDeposited($dateDeposited) {
        $this->dateDeposited = $dateDeposited;

        return $this;
    }

    /**
     * Get dateDeposited.
     *
     * @return DateTime
     */
    public function getDateDeposited() {
        return $this->dateDeposited;
    }

    /**
     * Set checksumType.
     *
     * @param string $checksumType
     *
     * @return Content
     */
    public function setChecksumType($checksumType) {
        $this->checksumType = $checksumType;

        return $this;
    }

    /**
     * Get checksumType.
     *
     * @return string
     */
    public function getChecksumType() {
        return $this->checksumType;
    }

    /**
     * Set checksumValue.
     *
     * @param string $checksumValue
     *
     * @return Content
     */
    public function setChecksumValue($checksumValue) {
        $this->checksumValue = $checksumValue;

        return $this;
    }

    /**
     * Get checksumValue.
     *
     * @return string
     */
    public function getChecksumValue() {
        return $this->checksumValue;
    }

    /**
     * Set recrawl.
     *
     * @param bool $recrawl
     *
     * @return Content
     */
    public function setRecrawl($recrawl) {
        $this->recrawl = $recrawl;

        return $this;
    }

    /**
     * Get recrawl.
     *
     * @return bool
     */
    public function getRecrawl() {
        return $this->recrawl;
    }

    /**
     * Set deposit.
     *
     * @param Deposit $deposit
     *
     * @return Content
     */
    public function setDeposit(Deposit $deposit = null) {
        $this->deposit = $deposit;
        if ($deposit !== null) {
            $deposit->addContent($this);
        }

        return $this;
    }

    /**
     * Get deposit.
     *
     * @return Deposit
     */
    public function getDeposit() {
        return $this->deposit;
    }

    /**
     * Set au.
     *
     * @param Au $au
     *
     * @return Content
     */
    public function setAu(Au $au = null) {
        $this->au = $au;
        if ($au !== null) {
            $au->addContent($this);
        }

        return $this;
    }

    /**
     * Get au.
     *
     * @return Au
     */
    public function getAu() {
        return $this->au;
    }

    /**
     * Set the deposit date. It cannot be changed once it is set.
     */
    public function setDepositDate() {
        if ($this->dateDeposited === null) {
            $this->dateDeposited = new DateTime();
        }
    }

    /**
     * Add contentProperties.
     *
     * @param ContentProperty $contentProperties
     *
     * @return Content
     */
    public function addContentProperty(ContentProperty $contentProperties) {
        $this->contentProperties[] = $contentProperties;

        return $this;
    }

    /**
     * Remove contentProperties.
     *
     * @param ContentProperty $contentProperties
     */
    public function removeContentProperty(ContentProperty $contentProperties) {
        $this->contentProperties->removeElement($contentProperties);
    }

    /**
     * Check if the content has a given property.
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasContentProperty($key) {
        return $this->contentProperties->containsKey($key);
    }

    /**
     * Convenience method. Get the filename of the content from the URL.
     *
     * @return string
     */
    public function getFilename() {
        return basename($this->url);
    }

    /**
     * Get contentProperties.
     *
     * @return Collection|ContentProperty
     */
    public function getContentProperties() {
        return $this->contentProperties;
    }

    /**
     * Get the value of a content property, optionally encoded to
     * LOCKSS standards.
     *
     * @param string $key
     * @param bool $encoded
     * @return string
     */
    public function getContentPropertyValue($key, $encoded = false) {
        $value = null;
        foreach ($this->getContentProperties() as $prop) {
            if ($prop->getPropertyKey() === $key) {
                $value = $prop->getPropertyValue();
                break;
            }
        }
        if ($encoded === false || $value === null) {
            return $value;
        }
        $callback = function ($matches) {
            $char = ord($matches[0]);

            return '%'.strtoupper(sprintf('%02x', $char));
        };

        return preg_replace_callback('/[^-_*a-zA-Z0-9]/', $callback, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getPln() {
        return $this->getDeposit()->getContentProvider()->getPln();
    }
}
