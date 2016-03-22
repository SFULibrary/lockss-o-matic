<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pln Properties are hierarchial.
 *
 * @ORM\Table(name="pln_properties")
 * @ORM\Entity
 */
class PlnProperty implements GetPlnInterface
{
	
	static $LIST_REQUIRED = array(
		'org.lockss.id.initialV3PeerList',
		'org.lockss.titleDbs',
		'org.lockss.plugin.registries'
	);


	/**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * The name of the property.
     *
     * @var string
     *
     * @ORM\Column(name="property_key", type="string", length=512, nullable=false)
     */
    private $propertyKey;

    /**
     * The value of the property. Parent properties don't have values. The value
     * may be an array.
     *
     * @var string|array
     *
     * @ORM\Column(name="property_value", type="array", nullable=false)
     */
    private $propertyValue;

    /**
     * The PLN for the property
     *
     * @var Pln
     *
     * @ORM\ManyToOne(targetEntity="Pln", inversedBy="plnProperties")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pln_id", referencedColumnName="id")
     * })
     */
    private $pln;

    public function __construct()
    {
		$this->propertyValue = array();
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
     * Set propertyKey
     *
     * @param string $propertyKey
     * @return PlnProperty
     */
    public function setPropertyKey($propertyKey)
    {
        $this->propertyKey = $propertyKey;

        return $this;
    }

    /**
     * Get propertyKey
     *
     * @return string
     */
    public function getPropertyKey()
    {
        return $this->propertyKey;
    }

    /**
     * Set propertyValue
     *
     * @param string|array $propertyValue
     * @return PlnProperty
     */
    public function setPropertyValue($propertyValue)
    {
		if(is_string($propertyValue)) {
			$this->propertyValue = array($propertyValue);
		} else {
			$this->propertyValue = $propertyValue;
		}
        return $this;
    }
	
	public function addPropertyValue($propertyValue) {
		$this->propertyValue[] = $propertyValue;
		return $this;
	}

    /**
     * Get propertyValue
     *
     * @return mixed
     */
    public function getPropertyValue()
    {
		if(! $this->isList()) {
			return $this->propertyValue[0];
		}
		return $this->propertyValue;
    }

    /**
     * Return true if the value of the property is a list.
     *
     * @return boolean
     */
    public function isList()
    {
		if(in_array($this->propertyKey, self::$LIST_REQUIRED)) {
			return true;
		}
        return count($this->propertyValue) > 1;
    }

    /**
     * Set pln
     *
     * @param Pln $pln
     * @return PlnProperty
     */
    public function setPln(Pln $pln = null)
    {
        $this->pln = $pln;
        $pln->addPlnProperty($this);

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
}
