<?php

namespace LOCKSSOMatic\UserBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;

/**
 * Message
 *
 * @ORM\Table()
 * @HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="MessageRepository")
 */
class Message
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="messages")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     * @var User
     */
    private $user;

    /**
     * @ORM\Column(name="seen", type="boolean")
     * @var boolean
     */
    private $seen;

    /**
     * @ORM\Column(name="content", type="text")
     * @var string
     */
    private $content;

    /**
     * @ORM\Column(name="created", type="datetime")
     * @var DateTime
     */
    private $created;

    public function __construct()
    {
        $this->seen = false;
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
     * Set seen
     *
     * @param boolean $seen
     * @return Message
     */
    public function setSeen($seen)
    {
        $this->seen = $seen;

        return $this;
    }

    /**
     * Get seen
     *
     * @return boolean 
     */
    public function getSeen()
    {
        return $this->seen;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Message
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set created
     * @PrePersist
     */
    public function setCreated()
    {
        $this->created = new DateTime();
    }

    /**
     * Get created
     *
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set user
     *
     * @param \LOCKSSOMatic\UserBundle\Entity\User $user
     * @return Message
     */
    public function setUser(\LOCKSSOMatic\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \LOCKSSOMatic\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
