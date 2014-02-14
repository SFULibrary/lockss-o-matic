<?php

namespace LOCKSSOMatic\CRUDBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Boxes
 *
 * @ORM\Table(name="boxes", indexes={@ORM\Index(name="plns_id_idx", columns={"plns_id"})})
 * @ORM\Entity
 */
class Boxes
{
	/**
	 * Property required for one-to-many relationship with BoxStatus.
	 * 
	 * @OneToMany(targetEntity="BoxStatus", mappedBy="boxStatus")
	 */
	protected $boxStatus;
	
	/**
	 * Initializes the $boxStatus property.
	 */
	public function __construct()
	{
		$this->boxStatus = new ArrayCollection();
	}

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="plns_id", type="integer", nullable=true)
     */
    private $plnsId;

    /**
     * @var string
     *
     * @ORM\Column(name="hostname", type="text", nullable=true)
     */
    private $hostname;

    /**
     * @var string
     *
     * @ORM\Column(name="ip_address", type="text", nullable=true)
     */
    private $ipAddress;



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
     * Set plnsId
     *
     * @param integer $plnsId
     * @return Boxes
     */
    public function setPlnsId($plnsId)
    {
        $this->plnsId = $plnsId;

        return $this;
    }

    /**
     * Get plnsId
     *
     * @return integer 
     */
    public function getPlnsId()
    {
        return $this->plnsId;
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
}
