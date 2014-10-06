<?php

namespace LOCKSSOMatic\CRUDBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BoxStatus
 */
class BoxStatus
{
    /**
     * @var integer
     */
    private $id;

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
     * @var \LOCKSSOMatic\CRUDBundle\Entity\Boxes
     */
    private $box;


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
     * Set queryDate
     *
     * @param \DateTime $queryDate
     * @return BoxStatus
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
     * @return BoxStatus
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
     * @return BoxStatus
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
     * Set box
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\Boxes $box
     * @return BoxStatus
     */
    public function setBox(\LOCKSSOMatic\CRUDBundle\Entity\Boxes $box = null)
    {
        $this->box = $box;

        return $this;
    }

    /**
     * Get box
     *
     * @return \LOCKSSOMatic\CRUDBundle\Entity\Boxes 
     */
    public function getBox()
    {
        return $this->box;
    }
}
