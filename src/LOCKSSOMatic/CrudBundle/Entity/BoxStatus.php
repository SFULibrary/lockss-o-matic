<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * BoxStatus
 *
 * @ORM\Table(name="box_status")
 * @ORM\Entity
 */
class BoxStatus implements GetPlnInterface
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
     * @var Box
     *
     * @ORM\ManyToOne(targetEntity="Box", inversedBy="status")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="box_id", referencedColumnName="id")
     * })
     */
    private $box;



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
     * @param DateTime $queryDate
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
     * @param Box $box
     * @return BoxStatus
     */
    public function setBox(Box $box = null)
    {
        $this->box = $box;
        $box->addStatus($this);

        return $this;
    }

    /**
     * Get box
     *
     * @return Box
     */
    public function getBox()
    {
        return $this->box;
    }

    /**
     * {@inheritDoc}
     */
    public function getPln()
    {
        return $this->getBox()->getPln();
    }


}
