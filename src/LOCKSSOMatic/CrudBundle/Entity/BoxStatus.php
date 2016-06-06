<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * BoxStatus.
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

    public function __construct()
    {
        $this->success = false;
        $this->caches = array();
    }

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
     * Add cach.
     *
     * @param CacheStatus $cach
     *
     * @return BoxStatus
     */
    public function addCache(CacheStatus $cache)
    {
        $this->caches[] = $cache;

        return $this;
    }

    /**
     * Remove cach.
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
