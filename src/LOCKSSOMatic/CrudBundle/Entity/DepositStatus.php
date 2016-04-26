<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * DepositStatus
 *
 * @ORM\Table(name="deposit_status")
 * @ORM\Entity
 */
class DepositStatus implements GetPlnInterface {

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @var Deposit
	 *
	 * @ORM\ManyToOne(targetEntity="Deposit")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="deposit_id", referencedColumnName="id")
	 * })
	 */
	private $deposit;
    
    /**
     * @var int
     * @ORM\Column(name="agreement", type="float")
     */
    private $agreement;

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
		return $this->deposit->getPln();
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
     * Set agreement
     *
     * @param integer $agreement
     *
     * @return DepositStatus
     */
    public function setAgreement($agreement)
    {
        $this->agreement = $agreement;

        return $this;
    }

    /**
     * Get agreement
     *
     * @return integer
     */
    public function getAgreement()
    {
        return $this->agreement;
    }

    /**
     * Set queryDate
     *
     * @param \DateTime $queryDate
     *
     * @return DepositStatus
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
     * Set status
     *
     * @param array $status
     *
     * @return DepositStatus
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return array
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set deposit
     *
     * @param \LOCKSSOMatic\CrudBundle\Entity\Deposit $deposit
     *
     * @return DepositStatus
     */
    public function setDeposit(\LOCKSSOMatic\CrudBundle\Entity\Deposit $deposit = null)
    {
        $this->deposit = $deposit;

        return $this;
    }

    /**
     * Get deposit
     *
     * @return \LOCKSSOMatic\CrudBundle\Entity\Deposit
     */
    public function getDeposit()
    {
        return $this->deposit;
    }
}
