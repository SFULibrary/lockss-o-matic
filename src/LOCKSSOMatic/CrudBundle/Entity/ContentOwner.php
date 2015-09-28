<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

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
     * @var integer
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
     * @ORM\Column(name="email_address", type="text", nullable=false)
     * @Assert\Email(
     *  strict = true
     * )
     */
    private $emailAddress;

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
     * @return ContentOwner
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
     * @return ContentOwner
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
     * Set plugin
     *
     * @param Plugin $plugin
     * @return ContentOwner
     */
    public function setPlugin(Plugin $plugin = null)
    {
        $this->plugin = $plugin;

        return $this;
    }

    public function __toString() {
        return $this->name;
    }
}
