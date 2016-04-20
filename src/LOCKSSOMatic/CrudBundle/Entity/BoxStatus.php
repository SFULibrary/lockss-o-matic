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
class BoxStatus implements GetPlnInterface {

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @var Box
	 *
	 * @ORM\ManyToOne(targetEntity="Box")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="box_id", referencedColumnName="id")
	 * })
	 */
	private $box;

	/**
	 * @var DateTime
	 *
	 * @ORM\Column(name="query_date", type="datetime", nullable=false)
	 */
	private $queryDate;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="status", type="array", nullable=true)
	 */
	private $status;
    
    public function __construct() {
        $this->status = array();
    }

	public function getPln() {
		return $this->box->getPln();
	}

	/**
	 * Get id
	 *
	 * @return integer 
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Set queryDate
	 *
	 * @param \DateTime $queryDate
	 * @return BoxStatus
	 */
	public function setQueryDate($queryDate) {
		$this->queryDate = $queryDate;

		return $this;
	}

	/**
	 * Get queryDate
	 *
	 * @return \DateTime 
	 */
	public function getQueryDate() {
		return $this->queryDate;
	}

	/**
	 * Set status
	 *
	 * @param array $status
	 * @return BoxStatus
	 */
	public function setStatus($status) {
		$this->status = $status;

		return $this;
	}

	/**
	 * Get status
	 *
	 * @return array 
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * Set box
	 *
	 * @param Box $box
	 * @return BoxStatus
	 */
	public function setBox(Box $box = null) {
		$this->box = $box;

		return $this;
	}

	/**
	 * Get box
	 *
	 * @return Box 
	 */
	public function getBox() {
		return $this->box;
	}

	/**
	 * @param string $name
	 * @return string|null
	 */
	private function getStatusValue($name) {
		if (!array_key_exists($name, $this->status)) {
			return null;
		}
		return $this->status[$name];
	}
	
	public function getActiveCount() {
		return $this->getStatusValue('activeCount');
	}
	
	public function getFree() {
		return $this->getStatusValue('free');
	}
	
	public function getSize() {
		return $this->getStatusValue('size');
	}
	
	public function getUsed() {
		return $this->getStatusValue('used');
	}

}
