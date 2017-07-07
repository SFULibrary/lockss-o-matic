<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * A PLN has multiple boxes, and each box has one or more caches (disk space).
 * Checking a box status is really checking the status of each cache in the box.
 *
 *
 * @ORM\Table(name="cache_status")
 * @ORM\Entity
 */
class CacheStatus
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
     * @var BoxStatus
     *
     * @ORM\ManyToOne(targetEntity="BoxStatus", inversedBy="caches")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="boxstatus_id", referencedColumnName="id")
     * })
     */
    private $boxStatus;

    /**
     * The response from LOCKSS for one cache.
     *
     * @var array
     *
     * @ORM\Column(name="response", type="array", nullable=false)
     */
    private $response;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set response.
     *
     * @param array $response
     *
     * @return CacheStatus
     */
    public function setResponse($response) {
        $this->response = $response;

        return $this;
    }

    /**
     * Get response.
     *
     * @return array
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     * Set boxStatus.
     *
     * @param BoxStatus $boxStatus
     *
     * @return CacheStatus
     */
    public function setBoxStatus(BoxStatus $boxStatus = null) {
        $this->boxStatus = $boxStatus;

        return $this;
    }

    /**
     * Get boxStatus.
     *
     * @return BoxStatus
     */
    public function getBoxStatus() {
        return $this->boxStatus;
    }

    /**
     * Get a list of the keys in the cache status response.
     *
     * @return array
     */
    public function getStatusKeys() {
        return array_keys($this->response);
    }

    /**
     * @param string $name
     *
     * @return string|null
     */
    public function getStatusValue($name) {
        if (!array_key_exists($name, $this->response)) {
            return;
        }

        return $this->response[$name];
    }

    /**
     * Return the number of active AUs in the cache.
     *
     * @return type
     */
    public function getActiveCount() {
        return $this->getStatusValue('activeCount');
    }

    /**
     * Get the free space in the cache.
     *
     * @return int
     */
    public function getFree() {
        return $this->getStatusValue('free');
    }

    /**
     * Get the total size of the cache.
     *
     * @return int
     */
    public function getSize() {
        return $this->getStatusValue('size');
    }

    /**
     * Get the space used in the cache.
     *
     * @return int
     */
    public function getUsed() {
        return $this->getStatusValue('used');
    }
}
