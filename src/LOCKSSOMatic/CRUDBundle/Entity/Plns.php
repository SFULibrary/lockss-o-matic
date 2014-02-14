<?php

namespace LOCKSSOMatic\CRUDBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Plns
 *
 * @ORM\Table(name="plns")
 * @ORM\Entity
 */
class Plns
{
	/**
	 * Property required for one-to-many relationship with PlnProperties.
	 * 
	 * @OneToMany(targetEntity="PlnProperties", mappedBy="plnProperties")
	 */
	protected $plnProperties;

	/**
	 * Property required for one-to-many relationship with ExternalTitleDbs.
	 * 
	 * @OneToMany(targetEntity="ExternalTitleDbs", mappedBy="externalTitleDbs")
	 */
	protected $externalTitleDbs;

	/**
	 * Property required for one-to-many relationship with Boxes.
	 * 
	 * @OneToMany(targetEntity="Boxes", mappedBy="boxes")
	 */
	protected $boxes;

	/**
	 * Initializes the $plnProperties and $externalTitleDbs properties.
	 */

	public function __construct()
	{
        $this->auProperties = new ArrayCollection();
        $this->externalTitleDbs = new ArrayCollection();
        $this->boxes = new ArrayCollection();
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
     * @var string
     *
     * @ORM\Column(name="props_path", type="text", nullable=true)
     */
    private $propsPath;



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
     * @return Plns
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

    /**
     * Set propsPath
     *
     * @param string $propsPath
     * @return Plns
     */
    public function setPropsPath($propsPath)
    {
        $this->propsPath = $propsPath;

        return $this;
    }

    /**
     * Get propsPath
     *
     * @return string 
     */
    public function getPropsPath()
    {
        return $this->propsPath;
    }
}
