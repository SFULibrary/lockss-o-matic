<?php

namespace LOCKSSOMatic\CRUDBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Plns
 *
 * @ORM\Table(name="plns")
 * @ORM\Entity
 */
class Plns
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
     * @ORM\Column(name="name", type="text", nullable=true)
     */
    private $name;

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
     * @return Plns
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
     * Set propsPath
     *
     * @param string $propsPath
     * @return Plns
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
}
