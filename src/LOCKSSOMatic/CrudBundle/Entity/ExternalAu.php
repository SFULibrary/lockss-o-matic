<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ExternalAu
 *
 * @ORM\Table(name="external_aus", indexes={@ORM\Index(name="IDX_8D3A7BF8C8BA1A08", columns={"pln_id"})})
 * @ORM\Entity
 */
class ExternalAu
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="pln_id", type="integer", nullable=true)
     */
    private $plnId;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="text", nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="auid", type="text", nullable=true)
     */
    private $auid;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="text", nullable=true)
     */
    private $path;



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
     * Set plnId
     *
     * @param integer $plnId
     * @return ExternalAu
     */
    public function setPlnId($plnId)
    {
        $this->plnId = $plnId;

        return $this;
    }

    /**
     * Get plnId
     *
     * @return integer 
     */
    public function getPlnId()
    {
        return $this->plnId;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return ExternalAu
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set auid
     *
     * @param string $auid
     * @return ExternalAu
     */
    public function setAuid($auid)
    {
        $this->auid = $auid;

        return $this;
    }

    /**
     * Get auid
     *
     * @return string 
     */
    public function getAuid()
    {
        return $this->auid;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return ExternalAu
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }
}
