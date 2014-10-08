<?php

namespace LOCKSSOMatic\CRUDBundle\Entity;

use DateTime;

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
     * @var DateTime
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
     * @var Boxes
     */
    private $box;


    /**
     * Stringify the entity
     *
     * @return string
     */
    public function __toString()
    {
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
     * @return BoxStatus
     */
    public function setQueryDate()
    {
        $this->queryDate = new DateTime();

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
     * @param Boxes $box
     * @return BoxStatus
     */
    public function setBox(Boxes $box = null)
    {
        $this->box = $box;

        return $this;
    }

    /**
     * Get box
     *
     * @return Boxes
     */
    public function getBox()
    {
        return $this->box;
    }
}
