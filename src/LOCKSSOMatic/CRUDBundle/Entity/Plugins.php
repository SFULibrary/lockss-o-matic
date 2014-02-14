<?php

namespace LOCKSSOMatic\CRUDBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Plugins
 *
 * @ORM\Table(name="plugins")
 * @ORM\Entity
 */
class Plugins
{
	/**
	 * Property required for one-to-many relationship with AuProperties.
	 * 
	 * @OneToMany(targetEntity="AuProperties", mappedBy="auProperties")
	 */
	protected $pluginProperties;

	/**
	 * Initializes the $pluginProperties property.
	 */
	public function __construct()
	{
        $this->pluginProperties = new ArrayCollection();
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
     * @var string
     *
     * @ORM\Column(name="name", type="text", nullable=true)
     */
    private $name;



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
     * Set name
     *
     * @param string $name
     * @return Plugins
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }
}
