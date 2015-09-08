<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ExternalTitleDb
 *
 * @ORM\Table(name="external_title_dbs", indexes={@ORM\Index(name="IDX_D40D047CC8BA1A08", columns={"pln_id"})})
 * @ORM\Entity
 */
class ExternalTitleDb
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
     * @var string
     *
     * @ORM\Column(name="path", type="text", nullable=false)
     */
    private $path;

    /**
     * @var Pln
     *
     * @ORM\ManyToOne(targetEntity="Pln", inversedBy="externalTitleDbs")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pln_id", referencedColumnName="id")
     * })
     */
    private $pln;

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
     * @return ExternalTitleDb
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
     * @param Pln $pln
     * @return ExternalTitleDb
     */
    public function setPln(Pln $pln = null)
    {
        $this->pln = $pln;

        return $this;
    }

    /**
     * Get pln
     *
     * @return Pln
     */
    public function getPln()
    {
        return $this->pln;
    }
}
