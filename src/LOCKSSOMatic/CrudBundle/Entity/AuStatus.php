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
 * AuStatus captures the status of one AU on each box.
 *
 * @ORM\Table(name="au_status")
 * @ORM\Entity
 */
class AuStatus implements GetPlnInterface
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
     * @var string
     * 
     * @ORM\Column(name="errors", type="array")
     */
    private $errors;

    /**
     * Build a new AuStatus.
     */
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
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set queryDate.
     *
     * @param DateTime $queryDate
     *
     * @return AuStatus
     */
    public function setQueryDate($queryDate)
    {
        $this->queryDate = $queryDate;

        return $this;
    }

    /**
     * Get queryDate.
     *
     * @return DateTime
     */
    public function getQueryDate()
    {
        return $this->queryDate;
    }

    /**
     * Set propertyValue.
     *
     * @param string $status
     *
     * @return AuStatus
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get propertyValue.
     *
     * @return array
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set au.
     *
     * @param Au $au
     *
     * @return AuStatus
     */
    public function setAu(Au $au = null)
    {
        $this->au = $au;
        $au->addAuStatus($this);

        return $this;
    }

    /**
     * Get au.
     *
     * @return Au
     */
    public function getAu()
    {
        return $this->au;
    }

    /**
     * {@inheritdoc}
     */
    public function getPln()
    {
        if ($this->au === null) {
            return;
        }

        return $this->getAu()->getPln();
    }

    /**
     * Set errors.
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
     * Get errors.
     *
     * @return string
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Summarize the status of an au mapping states to counts.
     * 
     * @return array
     */
    public function summary()
    {
        $statuses = array();
        foreach ($this->status as $host => $response) {
            $state = $response['status'];
            if (!array_key_exists($state, $statuses)) {
                $statuses[$state] = 0;
            }
            ++$statuses[$state];
        }
        $status = '';
        foreach ($statuses as $state => $count) {
            $status = "{$state}: {$count}\n";
        }

        return $status;
    }
}
