<?php

namespace LOCKSSOMatic\CRUDBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @var \DateTime
     */
    private $dateDeposited;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $content;

    /**
     * @var \LOCKSSOMatic\CRUDBundle\Entity\ContentProviders
     */
    private $contentProvider;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->content = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * @param \DateTime $dateDeposited
     * @return Deposits
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
     * Add content
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\Content $content
     * @return Deposits
     */
    public function addContent(\LOCKSSOMatic\CRUDBundle\Entity\Content $content)
    {
        $this->content[] = $content;

        return $this;
    }

    /**
     * Remove content
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\Content $content
     */
    public function removeContent(\LOCKSSOMatic\CRUDBundle\Entity\Content $content)
    {
        $this->content->removeElement($content);
    }

    /**
     * Get content
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set contentProvider
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\ContentProviders $contentProvider
     * @return Deposits
     */
    public function setContentProvider(\LOCKSSOMatic\CRUDBundle\Entity\ContentProviders $contentProvider = null)
    {
        $this->contentProvider = $contentProvider;

        return $this;
    }

    /**
     * Get contentProvider
     *
     * @return \LOCKSSOMatic\CRUDBundle\Entity\ContentProviders 
     */
    public function getContentProvider()
    {
        return $this->contentProvider;
    }
}
