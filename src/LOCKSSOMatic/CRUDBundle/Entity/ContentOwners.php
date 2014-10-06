<?php

namespace LOCKSSOMatic\CRUDBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ContentOwners
 */
class ContentOwners
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $emailAddress;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $contentProviders;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->contentProviders = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return ContentOwners
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
     * Set emailAddress
     *
     * @param string $emailAddress
     * @return ContentOwners
     */
    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    /**
     * Get emailAddress
     *
     * @return string 
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * Add contentProviders
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\ContentProviders $contentProviders
     * @return ContentOwners
     */
    public function addContentProvider(\LOCKSSOMatic\CRUDBundle\Entity\ContentProviders $contentProviders)
    {
        $this->contentProviders[] = $contentProviders;

        return $this;
    }

    /**
     * Remove contentProviders
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\ContentProviders $contentProviders
     */
    public function removeContentProvider(\LOCKSSOMatic\CRUDBundle\Entity\ContentProviders $contentProviders)
    {
        $this->contentProviders->removeElement($contentProviders);
    }

    /**
     * Get contentProviders
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getContentProviders()
    {
        return $this->contentProviders;
    }
}
