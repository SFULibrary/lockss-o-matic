<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * A box in a network.
 *
 * @ORM\Table(name="boxes")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Box implements GetPlnInterface
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
     * The DNS name.
     *
     * @var string
     *
     * @ORM\Column(name="hostname", type="string", length=255, nullable=false)
     */
    private $hostname;

    /**
     * The protocol to use in the lockss.xml file. Defaults to TCP.
     *
     * @var string
     * @ORM\Column(name="protocol", type="string", length=16, nullable=false)
     */
    private $protocol;

    /**
     * The port used for the lockss.xml file.
     *
     * @var integer
     * @ORM\Column(name="port", type="integer", nullable=false)
     */
    private $port;

    /**
     * The box's IP address. The class will resolve it automatically from the
     * domain name if the ipAddress is null or blank.
     *
     * @var string
     *
     * @ORM\Column(name="ip_address", type="string", length=16, nullable=false)
     */
    private $ipAddress;

    /**
     * The username for LOCKSSOMatic to communicate with the box. Not in the
     * lockss.xml file.
     *
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=64, nullable=true)
     */
    private $username;

    /**
     * The password for LOCKSSOMatic to communicate with the box.
     *
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=64, nullable=true)
     */
    private $password;

    /**
     * The PLN this box is a part of.
     *
     * @var Pln
     *
     * @ORM\ManyToOne(targetEntity="Pln", inversedBy="boxes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pln_id", referencedColumnName="id")
     * })
     */
    private $pln;

    /**
     * Timestamped list of box status query results.
     *
     * @ORM\OneToMany(targetEntity="BoxStatus", mappedBy="box")
     * @var BoxStatus[]
     */
    private $status;
    
    public function __construct()
    {
        $this->status = new ArrayCollection();
        $this->protocol = 'TCP';
        $this->port = 9729;
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
     * @return Box
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
     * @return Box
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
     * @return Box
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
     * @return Box
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
     * Set pln
     *
     * @param Pln $pln
     * @return Box
     */
    public function setPln(Pln $pln = null)
    {
        $this->pln = $pln;
        $pln->addBox($this);
        
        return $this;
    }

    /**
     * Get pln
     *
     * @return Pln
     */
    public function getPln()
    {
        return $this->pln;
    }

    /**
     * Add status
     *
     * @param BoxStatus $status
     * @return Box
     */
    public function addStatus(BoxStatus $status)
    {
        $this->status[] = $status;

        return $this;
    }

    /**
     * Remove status
     *
     * @param BoxStatus $status
     */
    public function removeStatus(BoxStatus $status)
    {
        $this->status->removeElement($status);
    }

    /**
     * Get status
     *
     * @return Collection
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set protocol
     *
     * @param string $protocol
     * @return Box
     */
    public function setProtocol($protocol)
    {
        $this->protocol = $protocol;

        return $this;
    }

    /**
     * Get protocol
     *
     * @return string
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * Set port
     *
     * @param integer $port
     * @return Box
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * Get port
     *
     * @return integer
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Resolve the hostname into an ipAddress and save it. Called automatically
     * when saving the box via doctrine.
     *
     * @param boolean $force force the update, even if the ip is already known.
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function resolveHostname($force = false)
    {
        if ($force === true || $this->ipAddress === null || $this->ipAddress === '') {
            $ip = gethostbyname($this->hostname);
            if ($ip !== $this->hostname) {
                $this->ipAddress = $ip;
            }
        }
    }
}
