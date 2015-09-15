<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Deposit
 *
 * @ORM\Table(name="deposits", indexes={@ORM\Index(name="IDX_449E9C9ED5F0A8C4", columns={"content_provider_id"}), @ORM\Index(name="uuid", columns={"uuid"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Deposit
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
     * @ORM\Column(name="uuid", type="string", length=36, nullable=false)
     */
    private $uuid;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="summary", type="string", length=255, nullable=true)
     */
    private $summary;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="date_deposited", type="datetime", nullable=false)
     */
    private $dateDeposited;

    /**
     * @var ContentProvider
     *
     * @ORM\ManyToOne(targetEntity="ContentProvider", inversedBy="deposits")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="content_provider_id", referencedColumnName="id")
     * })
     */
    private $contentProvider;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Content", mappedBy="deposit")
     */
    private $content;

    public function __construct() {
        $this->content = new ArrayCollection();
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
     * Set uuid
     *
     * @param string $uuid
     * @return Deposit
     */
    public function setUuid($uuid)
    {
        $this->uuid = strtoupper($uuid);

        return $this;
    }

    /**
     * Get uuid
     *
     * @return string 
     */
    public function getUuid()
    {
        return strtoupper($this->uuid);
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Deposit
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
     * Set summary
     *
     * @param string $summary
     * @return Deposit
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * Get summary
     *
     * @return string 
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Set dateDeposited
     *
     * @param DateTime $dateDeposited
     * @return Deposit
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
     * Set contentProvider
     *
     * @param ContentProvider $contentProvider
     * @return Deposit
     */
    public function setContentProvider(ContentProvider $contentProvider = null)
    {
        $this->contentProvider = $contentProvider;

        return $this;
    }

    /**
     * Get contentProvider
     *
     * @return ContentProvider
     */
    public function getContentProvider()
    {
        return $this->contentProvider;
    }

    /**
     * Add deposits
     *
     * @param Content $content
     * @return Deposit
     */
    public function addContent(Content $content)
    {
        $this->content[] = $content;

        return $this;
    }

    /**
     * Remove deposits
     *
     * @param Content $content
     */
    public function removeContent(Content $content)
    {
        $this->content->removeElement($content);
    }

    /**
     * Get deposits
     *
     * @return Collection
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @ORM\prePersist
     */
    public function setDepositDate() {
        if($this->dateDeposited === null) {
            $this->dateDeposited = new DateTime();
        }
    }
}
