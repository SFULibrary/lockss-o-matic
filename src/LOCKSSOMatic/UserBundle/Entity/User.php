<?php

namespace LOCKSSOMatic\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use LOCKSSOMatic\CrudBundle\Entity\Deposit;

/**
 * User
 *
 * @ORM\Table(name="lom_user")
 * @ORM\Entity(repositoryClass="UserRepository")
 */
class User extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="fullname", type="string", length=128)
     * @var string
     */
    private $fullname;
    
    /**
     * @ORM\Column(name="institution", type="string", length=128)
     * @var string
     */
    private $institution;

    /**
     * @ORM\OneToMany(targetEntity="LOCKSSOMatic\CrudBundle\Entity\Deposit", mappedBy="user")
     */
    private $deposits;

    public function __construct() {
        parent::__construct();
        $this->fullname = '';
        $this->institution = '';
        $this->deposits = new ArrayCollection();
    }
    
    public function setEmail($email) {
        parent::setEmail($email);
        $this->setUsername($email);
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
     * Set fullname
     *
     * @param string $fullname
     * @return User
     */
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;

        return $this;
    }

    /**
     * Get fullname
     *
     * @return string 
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * Set institution
     *
     * @param string $institution
     * @return User
     */
    public function setInstitution($institution)
    {
        $this->institution = $institution;

        return $this;
    }

    /**
     * Get institution
     *
     * @return string 
     */
    public function getInstitution()
    {
        return $this->institution;
    }

    /**
     * Add deposits
     *
     * @param \LOCKSSOMatic\CrudBundle\Entity\Deposit $deposits
     * @return User
     */
    public function addDeposit(\LOCKSSOMatic\CrudBundle\Entity\Deposit $deposits)
    {
        $this->deposits[] = $deposits;

        return $this;
    }

    /**
     * Remove deposits
     *
     * @param \LOCKSSOMatic\CrudBundle\Entity\Deposit $deposits
     */
    public function removeDeposit(\LOCKSSOMatic\CrudBundle\Entity\Deposit $deposits)
    {
        $this->deposits->removeElement($deposits);
    }

    /**
     * Get deposits
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDeposits()
    {
        return $this->deposits;
    }
}
