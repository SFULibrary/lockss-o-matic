<?php

namespace LOCKSSOMatic\CRUDBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Boxes
 */
class Boxes
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $hostname;

    /**
     * @var string
     */
    private $ipAddress;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $boxStatus;

    /**
     * @var \LOCKSSOMatic\CRUDBundle\Entity\Plns
     */
    private $pln;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->boxStatus = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Stringify the entity
     * 
     * @return string
     */
    public function __toString() {
        return $this->hostname;
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
     * Set hostname
     *
     * @param string $hostname
     * @return Boxes
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;

        return $this;
    }

    /**
     * Get hostname
     *
     * @return string 
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * Set ipAddress
     *
     * @param string $ipAddress
     * @return Boxes
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    /**
     * Get ipAddress
     *
     * @return string 
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return Boxes
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Boxes
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Add boxStatus
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\BoxStatus $boxStatus
     * @return Boxes
     */
    public function addBoxStatus(\LOCKSSOMatic\CRUDBundle\Entity\BoxStatus $boxStatus)
    {
        $this->boxStatus[] = $boxStatus;

        return $this;
    }

    /**
     * Remove boxStatus
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\BoxStatus $boxStatus
     */
    public function removeBoxStatus(\LOCKSSOMatic\CRUDBundle\Entity\BoxStatus $boxStatus)
    {
        $this->boxStatus->removeElement($boxStatus);
    }

    /**
     * Get boxStatus
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBoxStatus()
    {
        return $this->boxStatus;
    }

    /**
     * Set pln
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\Plns $pln
     * @return Boxes
     */
    public function setPln(\LOCKSSOMatic\CRUDBundle\Entity\Plns $pln = null)
    {
        $this->pln = $pln;

        return $this;
    }

    /**
     * Get pln
     *
     * @return \LOCKSSOMatic\CRUDBundle\Entity\Plns 
     */
    public function getPln()
    {
        return $this->pln;
    }
}
