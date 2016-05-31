<?php

namespace LOCKSSOMatic\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use LOCKSSOMatic\CrudBundle\Entity\Deposit;

/**
 * User.
 *
 * @ORM\Table(name="lom_user")
 * @ORM\Entity(repositoryClass="UserRepository")
 */
class User extends BaseUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="fullname", type="string", length=128)
     *
     * @var string
     */
    private $fullname;

    /**
     * @ORM\Column(name="institution", type="string", length=128)
     *
     * @var string
     */
    private $institution;

    /**
     * @ORM\OneToMany(targetEntity="LOCKSSOMatic\CrudBundle\Entity\Deposit", mappedBy="user")
     *
     * @var Deposit[]
     */
    private $deposits;

    /**
     * @ORM\OneToMany(targetEntity="Message", mappedBy="user")
     *
     * @var Message[]
     */
    private $messages;

    public function __construct()
    {
        parent::__construct();
        $this->fullname = '';
        $this->institution = '';
        $this->deposits = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    public function setEmail($email)
    {
        parent::setEmail($email);
        $this->setUsername($email);
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
     * Set fullname.
     *
     * @param string $fullname
     *
     * @return User
     */
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;

        return $this;
    }

    /**
     * Get fullname.
     *
     * @return string
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * Set institution.
     *
     * @param string $institution
     *
     * @return User
     */
    public function setInstitution($institution)
    {
        $this->institution = $institution;

        return $this;
    }

    /**
     * Get institution.
     *
     * @return string
     */
    public function getInstitution()
    {
        return $this->institution;
    }

    /**
     * Add deposits.
     *
     * @param Deposit $deposits
     *
     * @return User
     */
    public function addDeposit(Deposit $deposits)
    {
        $this->deposits[] = $deposits;

        return $this;
    }

    /**
     * Remove deposits.
     *
     * @param Deposit $deposits
     */
    public function removeDeposit(Deposit $deposits)
    {
        $this->deposits->removeElement($deposits);
    }

    /**
     * Get deposits.
     *
     * @return Deposit[]
     */
    public function getDeposits()
    {
        return $this->deposits;
    }

    /**
     * Add messages.
     *
     * @param Message $messages
     *
     * @return User
     */
    public function addMessage(Message $messages)
    {
        $this->messages[] = $messages;

        return $this;
    }

    /**
     * Remove messages.
     *
     * @param Message $messages
     */
    public function removeMessage(Message $messages)
    {
        $this->messages->removeElement($messages);
    }

    /**
     * Get messages.
     *
     * @return Message[]
     */
    public function getMessages($seen = null)
    {
        if ($seen === null) {
            return $this->messages;
        }

        return $this->messages->filter(function ($message) use ($seen) {
            return $message->getSeen() === $seen;
        });
    }
}
