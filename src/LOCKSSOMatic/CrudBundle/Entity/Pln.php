<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pln
 *
 * @ORM\Table(name="plns")
 * @ORM\Entity
 */
class Pln
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
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="propServer", type="string", length=255, nullable=false)
     */
    private $propserver;

    /**
     * @var string
     *
     * @ORM\Column(name="props_path", type="text", nullable=true)
     */
    private $propsPath;



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
     * Set name
     *
     * @param string $name
     * @return Pln
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set propserver
     *
     * @param string $propserver
     * @return Pln
     */
    public function setPropserver($propserver)
    {
        $this->propserver = $propserver;

        return $this;
    }

    /**
     * Get propserver
     *
     * @return string 
     */
    public function getPropserver()
    {
        return $this->propserver;
    }

    /**
     * Set propsPath
     *
     * @param string $propsPath
     * @return Pln
     */
    public function setPropsPath($propsPath)
    {
        $this->propsPath = $propsPath;

        return $this;
    }

    /**
     * Get propsPath
     *
     * @return string 
     */
    public function getPropsPath()
    {
        return $this->propsPath;
    }

    public function __toString() {
        return $this->name;
    }
}
