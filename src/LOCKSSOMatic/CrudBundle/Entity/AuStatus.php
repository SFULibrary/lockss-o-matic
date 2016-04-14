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
class AuStatus implements GetPlnInterface {

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
	public function getId() {
		return $this->id;
	}

	/**
	 * Set queryDate
	 *
	 * @param DateTime $queryDate
	 * @return AuStatus
	 */
	public function setQueryDate($queryDate) {
		$this->queryDate = $queryDate;

		return $this;
	}

	/**
	 * Get queryDate
	 *
	 * @return DateTime
	 */
	public function getQueryDate() {
		return $this->queryDate;
	}

	/**
	 * Set propertyValue
	 *
	 * @param string $status
	 * @return AuStatus
	 */
	public function setStatus($status) {
		$this->status = $status;

		return $this;
	}

	/**
	 * Get propertyValue
	 *
	 * @return string
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * Set au
	 *
	 * @param Au $au
	 * @return AuStatus
	 */
	public function setAu(Au $au = null) {
		$this->au = $au;
		$au->addAuStatus($this);

		return $this;
	}

	/**
	 * Get au
	 *
	 * @return Au
	 */
	public function getAu() {
		return $this->au;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPln() {
		return $this->getAu()->getPln();
	}

	/**
	 * Set box
	 *
	 * @param Box $box
	 * @return AuStatus
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

	public function getContentSize() {
		return $this->getStatusValue('contentSize');
	}

	public function getDiskUsage() {
		return $this->getStatusValue('diskUsage');
	}

	public function getLastCrawl() {
		return $this->getStatusValue('lastCompletedCrawl');
	}
	
	public function getLastCrawlResult() {
		return $this->getStatusValue('lastCrawlResult');
	}

	public function getLastPoll() {
		return $this->getStatusValue('lastPoll');
	}

	public function getLastPollResult() {
		return $this->getStatusValue('lastPollResult');
	}

	public function getAuStatus() {
		return $this->getStatusValue('status');
	}
}
