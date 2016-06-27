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
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * BoxStatus. The status of a box is a collection of the box cache statuses.
 *
 * @ORM\Table(name="box_status")
 * @ORM\Entity
 */
class BoxStatus implements GetPlnInterface
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
     * @var Box
     *
     * @ORM\ManyToOne(targetEntity="Box", inversedBy="status")
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
     * @var bool
     * 
     * @ORM\Column(name="success", type="boolean")
     */
    private $success;

    /**
     * @var Collection|CacheStatus
     * 
     * @ORM\OneToMany(targetEntity="CacheStatus", mappedBy="boxStatus", orphanRemoval=true)
     */
    private $caches;

    /**
     * @var string
     * 
     * @ORM\Column(name="errors", type="text", nullable=true)
     */
    private $errors;

    /**
     * Build a new box status.
     */
    public function __construct()
    {
        $this->success = false;
        $this->caches = array();
    }

    /**
     * {@inheritDocs}
     */
    public function getPln()
    {
        return $this->box->getPln();
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
     * Set queryDate.
     *
     * @param \DateTime $queryDate
     *
     * @return BoxStatus
     */
    public function setQueryDate(\DateTime $queryDate)
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
     * Set box.
     *
     * @param Box $box
     *
     * @return BoxStatus
     */
    public function setBox(Box $box = null)
    {
        $this->box = $box;

        return $this;
    }

    /**
     * Get box.
     *
     * @return Box
     */
    public function getBox()
    {
        return $this->box;
    }

    /**
     * Set success.
     *
     * @param bool $success
     *
     * @return BoxStatus
     */
    public function setSuccess($success)
    {
        $this->success = $success;

        return $this;
    }

    /**
     * Get success.
     *
     * @return bool
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * Add cache.
     *
     * @param CacheStatus $cache
     *
     * @return BoxStatus
     */
    public function addCache(CacheStatus $cache)
    {
        $this->caches[] = $cache;

        return $this;
    }

    /**
     * Remove cache.
     *
     * @param CacheStatus $cache
     */
    public function removeCache(CacheStatus $cache)
    {
        $this->caches->removeElement($cache);
    }

    /**
     * Get caches.
     *
     * @return Collection
     */
    public function getCaches()
    {
        return $this->caches;
    }

    /**
     * Set errors.
     *
     * @param string $errors
     *
     * @return BoxStatus
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
}
