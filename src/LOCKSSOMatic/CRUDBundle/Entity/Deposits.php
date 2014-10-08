<?php

namespace LOCKSSOMatic\CRUDBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use LOCKSSOMatic\CRUDBundle\Entity\ContentProviders;

/**
 * Deposits
 */
class Deposits
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $uuid;

    /**
     * @var string
     */
    private $title;

    /**
     * @var DateTime
     */
    private $dateDeposited;

    /**
     * @var Collection
     */
    private $content;

    /**
     * @var ContentProviders
     */
    private $contentProvider;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->content = new ArrayCollection();
    }

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
     * Set uuid
     *
     * @param string $uuid
     * @return Deposits
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
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
     * Set title
     *
     * @param string $title
     * @return Deposits
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
     * Set dateDeposited
     *
     * @param DateTime $dateDeposited
     * @return Deposits
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
     * Add content
     *
     * @param Content $content
     * @return Deposits
     */
    public function addContent(Content $content)
    {
        $this->content[] = $content;

        return $this;
    }

    /**
     * Remove content
     *
     * @param Content $content
     */
    public function removeContent(Content $content)
    {
        $this->content->removeElement($content);
    }

    /**
     * Get content
     *
     * @return Collection
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set contentProvider
     *
     * @param ContentProviders $contentProvider
     * @return Deposits
     */
    public function setContentProvider(ContentProviders $contentProvider = null)
    {
        $this->contentProvider = $contentProvider;

        return $this;
    }

    /**
     * Get contentProvider
     *
     * @return ContentProviders
     */
    public function getContentProvider()
    {
        return $this->contentProvider;
    }
}
