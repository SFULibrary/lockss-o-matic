<?php

namespace LOCKSSOMatic\CRUDBundle\Entity;

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
     * @var Plns
     */
    private $pln;


    /**
     * Stringify the entity
     *
     * @return string
     */
    public function __toString()
    {
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
     * @param Plns $pln
     * @return ExternalTitleDbs
     */
    public function setPln(Plns $pln = null)
    {
        $this->pln = $pln;

        return $this;
    }

    /**
     * Get pln
     *
     * @return Plns
     */
    public function getPln()
    {
        return $this->pln;
    }
}
