<?php

/*
 * The MIT License
 *
 * Copyright 2014-2016. Michael Joyce <ubermichael@gmail.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace LOCKSSOMatic\CrudBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * DepositStatus.
 *
 * @ORM\Table(name="deposit_status")
 * @ORM\Entity
 */
class DepositStatus implements GetPlnInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var Deposit
     *
     * @ORM\ManyToOne(targetEntity="Deposit", inversedBy="status")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="deposit_id", referencedColumnName="id")
     * })
     */
    private $deposit;

    /**
     * @var float
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
     * A deposit status is a big array.
     * 
     * @var array
     *
     * @ORM\Column(name="status", type="array", nullable=true)
     */
    private $status;

    /**
     * Build an empty status.
     */
    public function __construct()
    {
        $this->status = array();
    }

    /**
     * Get the PLN for the depositStatus's deposit.
     * 
     * @return type
     */
    public function getPln()
    {
        return $this->deposit->getPln();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set agreement.
     *
     * @param int $agreement
     *
     * @return DepositStatus
     */
    public function setAgreement($agreement)
    {
        $this->agreement = $agreement;

        return $this;
    }

    /**
     * Get agreement.
     *
     * @return int
     */
    public function getAgreement()
    {
        return $this->agreement;
    }

    /**
     * Set queryDate.
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
     * Get queryDate.
     *
     * @return \DateTime
     */
    public function getQueryDate()
    {
        return $this->queryDate;
    }

    /**
     * Set status.
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
     * Get status.
     *
     * @return array
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set deposit.
     *
     * @param Deposit $deposit
     *
     * @return DepositStatus
     */
    public function setDeposit(Deposit $deposit = null)
    {
        $this->deposit = $deposit;

        return $this;
    }

    /**
     * Get deposit.
     *
     * @return Deposit
     */
    public function getDeposit()
    {
        return $this->deposit;
    }
}
