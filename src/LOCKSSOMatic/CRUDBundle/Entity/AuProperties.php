<?php

namespace LOCKSSOMatic\CRUDBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AuProperties
 *
 * @ORM\Table(name="au_properties", indexes={@ORM\Index(name="aus_id_idx", columns={"aus_id"})})
 * @ORM\Entity
 */
class AuProperties
{
	
	/**
	* Property required for many-to-one relationship with Aus.
	* 
	* @ManyToOne(targetEntity="Aus", mappedBy="auProperties")
	* @JoinColumn(name="aus_id", referencedColumnName="id")
	*/
	protected $au;

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
     * @ORM\Column(name="aus_id", type="integer", nullable=true)
     */
    private $ausId;

    /**
     * @var integer
     *
     * @ORM\Column(name="parent_id", type="integer", nullable=true)
     */
    private $parentId;

    /**
     * @var string
     *
     * @ORM\Column(name="property_key", type="text", nullable=true)
     */
    private $propertyKey;

    /**
     * @var string
     *
     * @ORM\Column(name="property_value", type="text", nullable=true)
     */
    private $propertyValue;



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
     * Set ausId
     *
     * @param integer $ausId
     * @return AuProperties
     */
    public function setAusId($ausId)
    {
        $this->ausId = $ausId;

        return $this;
    }

    /**
     * Get ausId
     *
     * @return integer 
     */
    public function getAusId()
    {
        return $this->ausId;
    }

    /**
     * Set parentId
     *
     * @param integer $parentId
     * @return AuProperties
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * Get parentId
     *
     * @return integer 
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Set propertyKey
     *
     * @param string $propertyKey
     * @return AuProperties
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
     * @param string $propertyValue
     * @return AuProperties
     */
    public function setPropertyValue($propertyValue)
    {
        $this->propertyValue = $propertyValue;

        return $this;
    }

    /**
     * Get propertyValue
     *
     * @return string 
     */
    public function getPropertyValue()
    {
        return $this->propertyValue;
    }

    /**
     * Set au
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\Aus $au
     * @return AuProperties
     */
    public function setAu(\LOCKSSOMatic\CRUDBundle\Entity\Aus $au = null)
    {
        $this->au = $au;

        return $this;
    }

    /**
     * Get au
     *
     * @return \LOCKSSOMatic\CRUDBundle\Entity\Aus 
     */
    public function getAu()
    {
        return $this->au;
    }
}
