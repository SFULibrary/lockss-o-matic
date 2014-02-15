<?php

namespace LOCKSSOMatic\CRUDBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BoxStatus
 *
 * @ORM\Table(name="box_status", indexes={@ORM\Index(name="boxes_id_idx", columns={"boxes_id"})})
 * @ORM\Entity
 */
class BoxStatus
{
	/**
	* Property required for many-to-one relationship with Boxes.
	* 
	* @ManyToOne(targetEntity="Boxes", mappedBy="boxStatus")
	* @JoinColumn(name="boxes_id", referencedColumnName="id")
	*/
	protected $box;

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
     * @ORM\Column(name="boxes_id", type="integer", nullable=true)
     */
    private $boxesId;

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
     * Set boxesId
     *
     * @param integer $boxesId
     * @return BoxStatus
     */
    public function setBoxesId($boxesId)
    {
        $this->boxesId = $boxesId;

        return $this;
    }

    /**
     * Get boxesId
     *
     * @return integer 
     */
    public function getBoxesId()
    {
        return $this->boxesId;
    }

    /**
     * Set queryDate
     *
     * @param \DateTime $queryDate
     * @return BoxStatus
     */
    public function setQueryDate($queryDate)
    {
        $this->queryDate = new \DateTime();;

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
