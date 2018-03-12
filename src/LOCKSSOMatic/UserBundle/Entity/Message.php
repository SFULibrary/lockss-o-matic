<?php

namespace LOCKSSOMatic\UserBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Message.
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="MessageRepository")
 */
class Message
{
    /**
     * @var int
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
     *
     * @var User2
     */
    private $user;

    /**
     * @ORM\Column(name="seen", type="boolean")
     *
     * @var bool
     */
    private $seen;

    /**
     * @ORM\Column(name="content", type="text")
     *
     * @var string
     */
    private $content;

    /**
     * @ORM\Column(name="created", type="datetime")
     *
     * @var DateTime
     */
    private $created;

    /**
     * Construct a new message.
     */
    public function __construct() {
        $this->seen = false;
        $this->created = new DateTime();
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
     * Set seen.
     *
     * @param bool $seen
     *
     * @return Message
     */
    public function setSeen($seen) {
        $this->seen = $seen;

        return $this;
    }

    /**
     * Get seen.
     *
     * @return bool
     */
    public function getSeen() {
        return $this->seen;
    }

    /**
     * Set content.
     *
     * @param string $content
     *
     * @return Message
     */
    public function setContent($content) {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content.
     *
     * @return string
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * Get created.
     *
     * @return DateTime
     */
    public function getCreated() {
        return $this->created;
    }

    /**
     * Set user.
     *
     * @param User $user
     *
     * @return Message
     */
    public function setUser(User $user = null) {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return User
     */
    public function getUser() {
        return $this->user;
    }
}
