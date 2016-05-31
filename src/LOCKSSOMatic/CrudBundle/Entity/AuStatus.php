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
	 * @var DateTime
	 *
	 * @ORM\Column(name="query_date", type="datetime", nullable=false)
	 */
	private $queryDate;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="status", type="array")
	 */
	private $status;
    
    /**
     * @var String
     * 
     * @ORM\Column(name="errors", type="array")
     */
    private $errors;
    
    public function __construct()
    {
        $this->status = array();
        $this->errors = array();
    }
    
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
	 * @return array
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
        if($this->au === null) {
            return null;
        }
		return $this->getAu()->getPln();
	}

    /**
     * Set errors
     *
     * @param string $errors
     *
     * @return AuStatus
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     * Get errors
     *
     * @return string
     */
    public function getErrors()
    {
        return $this->errors;
    }
    
    public function summary() {
        $statuses = array();
        foreach($this->status as $host => $response) {
            $state = $response['status'];
            if( ! array_key_exists($state, $statuses)) {
                $statuses[$state] = 0;
            }
            $statuses[$state]++;
        }
        $status = "";
        foreach($statuses as $state => $count) {
            $status = "{$state}: {$count}\n";
        }
        return $status;
    }
}
