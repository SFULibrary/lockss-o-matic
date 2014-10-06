<?php

namespace LOCKSSOMatic\CRUDBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AuStatus
 */
class AuStatus
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $boxHostname;

    /**
     * @var \DateTime
     */
    private $queryDate;

    /**
     * @var string
     */
    private $propertyKey;

    /**
     * @var string
     */
    private $propertyValue;

    /**
     * @var \LOCKSSOMatic\CRUDBundle\Entity\Aus
     */
    private $au;

    /**
     * Stringify the entity
     * 
     * @return string
     */
    public function __toString() {
        return $this->propertyKey;
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
        $this->queryDate = $queryDate;

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

    /**
     * Set au
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\Aus $au
     * @return AuStatus
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
