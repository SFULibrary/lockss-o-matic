<?php

namespace LOCKSSOMatic\CRUDBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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
     * @var Collection
     */
    private $contentProviders;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->contentProviders = new ArrayCollection();
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
     * @param ContentProviders $contentProviders
     * @return ContentOwners
     */
    public function addContentProvider(ContentProviders $contentProviders)
    {
        $this->contentProviders[] = $contentProviders;

        return $this;
    }

    /**
     * Remove contentProviders
     *
     * @param ContentProviders $contentProviders
     */
    public function removeContentProvider(ContentProviders $contentProviders)
    {
        $this->contentProviders->removeElement($contentProviders);
    }

    /**
     * Get contentProviders
     *
     * @return Collection
     */
    public function getContentProviders()
    {
        return $this->contentProviders;
    }
}
