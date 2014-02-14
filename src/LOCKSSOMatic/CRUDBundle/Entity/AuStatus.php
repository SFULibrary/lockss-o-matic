<?php

namespace LOCKSSOMatic\CRUDBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AuStatus
 *
 * @ORM\Table(name="au_status", indexes={@ORM\Index(name="aus_id_idx", columns={"aus_id"})})
 * @ORM\Entity
 */
class AuStatus
{
	/**
	* Property required for many-to-one relationship with Deposits.
	* 
	* @ManyToOne(targetEntity="Aus", mappedBy="auStatus")
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
     * @var string
     *
     * @ORM\Column(name="box_hostname", type="text", nullable=true)
     */
    private $boxHostname;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="query_date", type="datetime", nullable=true)
     */
    private $queryDate;

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
     * @return AuStatus
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
     * Set boxHostname
     *
     * @param string $boxHostname
     * @return AuStatus
     */
    public function setBoxHostname($boxHostname)
    {
        $this->boxHostname = $boxHostname;

        return $this;
    }

    /**
     * Get boxHostname
     *
     * @return string 
     */
    public function getBoxHostname()
    {
        return $this->boxHostname;
    }

    /**
     * Set queryDate
     *
     * @param \DateTime $queryDate
     * @return AuStatus
     */
    public function setQueryDate($queryDate)
    {
        $this->queryDate = new \DateTime();

        return $this;
    }

    /**
     * Get queryDate
     *
     * @return \DateTime 
     */
    public function getQueryDate()
    {
        return $this->queryDate;
    }

    /**
     * Set propertyKey
     *
     * @param string $propertyKey
     * @return AuStatus
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
     * @return AuStatus
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
}
