<?php

namespace LOCKSSOMatic\CRUDBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ExternalTitleDbs
 */
class ExternalTitleDbs
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $path;

    /**
     * @var \LOCKSSOMatic\CRUDBundle\Entity\Plns
     */
    private $pln;


    /**
     * Stringify the entity
     * 
     * @return string
     */
    public function __toString() {
        return $this->path;
    }

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
     * Set path
     *
     * @param string $path
     * @return ExternalTitleDbs
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

    /**
     * Set pln
     *
     * @param \LOCKSSOMatic\CRUDBundle\Entity\Plns $pln
     * @return ExternalTitleDbs
     */
    public function setPln(\LOCKSSOMatic\CRUDBundle\Entity\Plns $pln = null)
    {
        $this->pln = $pln;

        return $this;
    }

    /**
     * Get pln
     *
     * @return \LOCKSSOMatic\CRUDBundle\Entity\Plns 
     */
    public function getPln()
    {
        return $this->pln;
    }
}
