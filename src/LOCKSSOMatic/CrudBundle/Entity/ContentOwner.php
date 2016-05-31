<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Content owner. Deposits are made by a content provider on behalf of a content
 * owner.
 *
 * @ORM\Table(name="content_owners")
 * @ORM\Entity
 */
class ContentOwner
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
     * Name of the content owner.
     *
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * Email address for the content owner.
     *
     * @var string
     *
     * @ORM\Column(name="email_address", type="text", nullable=true)
     * @Assert\Email(
     *  strict = true
     * )
     */
    private $emailAddress;

    /**
     * @ORM\OneToMany(targetEntity="ContentProvider", mappedBy="contentOwner")
     *
     * @var ArrayCollection
     */
    private $contentProviders;

    public function __construct()
    {
        $this->contentProviders = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return ContentOwner
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set emailAddress.
     *
     * @param string $emailAddress
     *
     * @return ContentOwner
     */
    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    /**
     * Get emailAddress.
     *
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Add contentProviders.
     *
     * @param \LOCKSSOMatic\CrudBundle\Entity\ContentProvider $contentProviders
     *
     * @return ContentOwner
     */
    public function addContentProvider(\LOCKSSOMatic\CrudBundle\Entity\ContentProvider $contentProviders)
    {
        $this->contentProviders[] = $contentProviders;

        return $this;
    }

    /**
     * Remove contentProviders.
     *
     * @param \LOCKSSOMatic\CrudBundle\Entity\ContentProvider $contentProviders
     */
    public function removeContentProvider(\LOCKSSOMatic\CrudBundle\Entity\ContentProvider $contentProviders)
    {
        $this->contentProviders->removeElement($contentProviders);
    }

    /**
     * Get contentProviders.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContentProviders()
    {
        return $this->contentProviders;
    }
}
