<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * AuStatus
 *
 * @ORM\Table(name="au_status")
 * @ORM\Entity
 */
class AuStatus implements GetPlnInterface
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
     * TODO - why isn't this a reference to the Box class?
     * @var string
     *
     * @ORM\Column(name="box_hostname", type="string", length=255, nullable=false)
     */
    private $boxHostname;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="query_date", type="datetime", nullable=false)
     */
    private $queryDate;

    /**
     * @var string
     *
     * @ORM\Column(name="property_key", type="string", length=255, nullable=false)
     */
    private $propertyKey;

    /**
     * @var string
     *
     * @ORM\Column(name="property_value", type="text", nullable=true)
     */
    private $propertyValue;

    /**
     * @var Au
     *
     * @ORM\ManyToOne(targetEntity="Au", inversedBy="auStatus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="au_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $au;



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
     * @param DateTime $queryDate
     * @return AuStatus
     */
    public function setQueryDate($queryDate)
    {
        $this->queryDate = $queryDate;

        return $this;
    }

    /**
     * Get queryDate
     *
     * @return DateTime
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

    /**
     * Set au
     *
     * @param Au $au
     * @return AuStatus
     */
    public function setAu(Au $au = null)
    {
        $this->au = $au;
        $au->addAuStatus($this);

        return $this;
    }

    /**
     * Get au
     *
     * @return Au
     */
    public function getAu()
    {
        return $this->au;
    }

    /**
     * {@inheritDoc}
     */
    public function getPln()
    {
        return $this->getAu()->getPln();
    }
}
