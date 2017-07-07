<?php


namespace LOCKSSOMatic\LogBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use LOCKSSOMatic\CRUDBundle\Entity\Pln;
use LOCKSSOMatic\UserBundle\Entity\User;

/**
 * LogEntry.
 *
 * @ORM\Table(name="log_entry")
 * @ORM\Entity(readOnly=true)
 */
class LogEntry
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
     * @var string
     * @ORM\Column(name="bundle", type="string", length=255, nullable=true)
     */
    private $bundle;

    /**
     * @var string
     * @ORM\Column(name="class", type="string", length=512, nullable=true)
     */
    private $class;

    /**
     * @var string
     * @ORM\Column(name="caller", type="string", length=512, nullable=true)
     */
    private $caller;

    /**
     * @var string
     * @ORM\Column(name="level", type="string", length=24, nullable=false)
     */
    private $level;

    /**
     * @ORM\Column(name="summary", type="text", nullable=false)
     *
     * @var string
     */
    private $summary;

    /**
     * @var string
     * @ORM\Column(name="message", type="text", nullable=true)
     */
    private $message;

    /**
     * @var DateTime
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    private $created;

    /**
     * @var Pln
     * @ORM\ManyToOne(targetEntity="LOCKSSOMatic\CrudBundle\Entity\Pln")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="pln_id", referencedColumnName="id")
     * })
     */
    private $pln;

    /**
     * @var string
     * @ORM\Column(name="user", type="string", length=127, nullable=true)
     */
    private $user;

    /**
     * @var string
     * @ORM\Column(name="ip", type="string", length=36, nullable=false)
     */
    private $ip;

    /**
     * Construct a log entry.
     */
    public function __construct() {
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
     * Set bundle.
     *
     * @param string $bundle
     *
     * @return LogEntry
     */
    public function setBundle($bundle) {
        $this->bundle = $bundle;

        return $this;
    }

    /**
     * Get bundle.
     *
     * @return string
     */
    public function getBundle() {
        if ($this->bundle) {
            return $this->bundle;
        }
        $matches = array();
        if (preg_match('{([a-zA-Z]*)Bundle}', $this->class, $matches)) {
            return $matches[1];
        }

        return '';
    }

    /**
     * Set class.
     *
     * @param string $class
     *
     * @return LogEntry
     */
    public function setClass($class) {
        $this->class = $class;

        return $this;
    }

    /**
     * Get class.
     *
     * @return string
     */
    public function getClass() {
        return $this->class;
    }

    /**
     * Set caller.
     *
     * @param string $caller
     *
     * @return LogEntry
     */
    public function setCaller($caller) {
        $this->caller = $caller;

        return $this;
    }

    /**
     * Get caller.
     *
     * @return string
     */
    public function getCaller() {
        return $this->caller;
    }

    /**
     * Set level.
     *
     * @param string $level
     *
     * @return LogEntry
     */
    public function setLevel($level) {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level.
     *
     * @return string
     */
    public function getLevel() {
        return $this->level;
    }

    /**
     * Set summary.
     *
     * @param string $summary
     *
     * @return LogEntry
     */
    public function setSummary($summary) {
        $this->summary = $summary;

        return $this;
    }

    /**
     * Get summary.
     *
     * @return string
     */
    public function getSummary() {
        return $this->summary;
    }

    /**
     * Set message.
     *
     * @param string $message
     *
     * @return LogEntry
     */
    public function setMessage($message) {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message.
     *
     * @return string
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * Set created. If the log entry already has a created time, nothing
     * happens.
     *
     * @return LogEntry
     */
    public function setCreated() {
        if ($this->created === null) {
            $this->created = new \DateTime();
        }

        return $this;
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
     * Set pln.
     *
     * @param Pln $pln
     *
     * @return LogEntry
     */
    public function setPln(Pln $pln = null) {
        $this->pln = $pln;

        return $this;
    }

    /**
     * Get pln.
     *
     * @return Pln
     */
    public function getPln() {
        return $this->pln;
    }

    /**
     * Set user.
     *
     * @param mixed $user
     *
     * @return LogEntry
     */
    public function setUser($user = null) {
        if ($user instanceof User) {
            $this->user = $user->getEmail();
        } else {
            $this->user = $user;
        }

        return $this;
    }

    /**
     * Get user.
     *
     * @return mixed
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Set ip.
     *
     * @param string $ip
     *
     * @return LogEntry
     */
    public function setIp($ip) {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip.
     *
     * @return string
     */
    public function getIp() {
        return $this->ip;
    }

    /**
     * Return a list of field names, in the order returned by toArray().
     *
     * @return array
     */
    public static function toArrayHeader() {
        return array(
            'created',
            'ip',
            'user',
            'level',
            'bundle',
            'class',
            'function',
            'pln',
            'summary',
            'message',
        );
    }

    /**
     * Return a list of fields, in the same order as toArrayHeader().
     *
     * @return type
     */
    public function toArray() {
        return array(
            $this->created->format('c'),
            $this->ip,
            $this->user,
            $this->level,
            $this->bundle,
            $this->class,
            $this->caller,
            $this->pln,
            $this->summary,
            $this->message,
        );
    }
}
