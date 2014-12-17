<?php

namespace LOCKSSOMatic\LoggingBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use LOCKSSOMatic\CRUDBundle\Entity\Plns;

/**
 * LogEntry
 * @ORM\Entity(repositoryClass="LogEntryRepository")
 */
class LogEntry
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $bundle;

    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $caller;

    /**
     * @var string
     */
    private $level;

    /**
     * @var string
     */
    private $summary;

    /**
     * @var string
     */
    private $message;

    /**
     * @var DateTime
     */
    private $created;

    /**
     * @var Plns
     */
    private $pln;

    /**
     * @var
     */
    private $user;

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
     * Set file
     *
     * @param string $file
     * @return LogEntry
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return string 
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set line
     *
     * @param integer $line
     * @return LogEntry
     */
    public function setLine($line)
    {
        $this->line = $line;

        return $this;
    }

    /**
     * Get line
     *
     * @return integer 
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * Set bundle
     *
     * @param string $bundle
     * @return LogEntry
     */
    public function setBundle($bundle)
    {
        $this->bundle = $bundle;

        return $this;
    }

    /**
     * Get bundle
     *
     * @return string 
     */
    public function getBundle()
    {
        if($this->bundle) {
            return $this->bundle;
        }
        $matches = array();
        if(preg_match('{([a-zA-Z]*)Bundle}', $this->class, $matches)) {
            return $matches[1];
        }
        return '';
        
    }

    /**
     * Set class
     *
     * @param string $class
     * @return LogEntry
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Get class
     *
     * @return string 
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set caller
     *
     * @param string $caller
     * @return LogEntry
     */
    public function setCaller($caller)
    {
        $this->caller = $caller;

        return $this;
    }

    /**
     * Get caller
     *
     * @return string 
     */
    public function getCaller()
    {
        return $this->caller;
    }

    /**
     * Set level
     *
     * @param string $level
     * @return LogEntry
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return string 
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set summary
     *
     * @param string $summary
     * @return LogEntry
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * Get summary
     *
     * @return string 
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return LogEntry
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set created
     *
     * @param DateTime $created
     * @return LogEntry
     */
    public function setCreated()
    {
        if($this->created === null) {
            $this->created = new \DateTime();
        }
        
        return $this;
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
     * Set pln
     *
     * @param Plns $pln
     * @return LogEntry
     */
    public function setPln(Plns $pln = null)
    {
        $this->pln = $pln;

        return $this;
    }

    /**
     * Get pln
     *
     * @return Plns 
     */
    public function getPln()
    {
        return $this->pln;
    }

    /**
     * Set user
     *
     * @param mixed $user
     * @return LogEntry
     */
    public function setUser($user = null)
    {
        if($user instanceof LOCKSSOMatic\UserBundle\Entity\User) {
            $this->user = $user->getEmail();
        } else {
            $this->user = $user;
        }
        return $this;
    }

    /**
     * Get user
     *
     * @return mixed 
     */
    public function getUser()
    {
        return $this->user;
    }
    
    public static function toArrayHeader() {
        return array(
            'created',
            'level',
            'bundle',
            'class',
            'function',
            'pln',
            'summary',
            'message',
        );
    }
    
    public function toArray() {
        return array(
            $this->created->format('c'),
            $this->level,
            $this->bundle,
            $this->class,
            $this->caller,
            $this->pln,
            $this->summary,
            $this->message
        );
    }
}
