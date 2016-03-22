<?php

use DateTime;
use LOCKSSOMatic\UserBundle\Entity\Message;
use LOCKSSOMatic\UserBundle\Entity\MessageRepository;
use LOCKSSOMatic\UserBundle\Entity\User;
use Symfony\Component\Security\Core\User\User as User2;

namespace LOCKSSOMatic\UserBundle\Entity;

/**
 * Message
 *
 * @ORM\Table()
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
     * @ORM\ManyToOne(targetEntity="User2", inversedBy="messages")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     * @var User2
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
        $this->created = new DateTime();
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
     * @param User $user
     * @return Message
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}
