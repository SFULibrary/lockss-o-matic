<?php


namespace LOCKSSOMatic\CrudBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use LOCKSSOMatic\CrudBundle\Entity\BoxStatus;
use LOCKSSOMatic\CrudBundle\Entity\CacheStatus;

/**
 * Description of CacheStatus
 *
 * @ORM\Table(name="cache_status")
 * @ORM\Entity
 */
class CacheStatus {

    /**
     * @var integer
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set response
     *
     * @param array $response
     *
     * @return CacheStatus
     */
    public function setResponse($response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Get response
     *
     * @return array
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Set boxStatus
     *
     * @param BoxStatus $boxStatus
     *
     * @return CacheStatus
     */
    public function setBoxStatus(BoxStatus $boxStatus = null)
    {
        $this->boxStatus = $boxStatus;

        return $this;
    }

    /**
     * Get boxStatus
     *
     * @return BoxStatus
     */
    public function getBoxStatus()
    {
        return $this->boxStatus;
    }
    
    public function getStatusKeys() {
        return array_keys($this->response);
    }
    
    /**
	 * @param string $name
	 * @return string|null
	 */
	public function getStatusValue($name) {
		if (!array_key_exists($name, $this->response)) {
			return null;
		}
		return $this->response[$name];
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
