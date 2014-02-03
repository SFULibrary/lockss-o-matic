<?php

namespace LOCKSSOMatic\CRUDBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WebFrameworkUsers
 *
 * @ORM\Table(name="web_framework_users")
 * @ORM\Entity
 */
class WebFrameworkUsers
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
     * @ORM\Column(name="foo", type="string", length=45, nullable=true)
     */
    private $foo;

    /**
     * @var string
     *
     * @ORM\Column(name="bar", type="string", length=45, nullable=true)
     */
    private $bar;

    /**
     * @var string
     *
     * @ORM\Column(name="etc", type="string", length=45, nullable=true)
     */
    private $etc;



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
     * Set foo
     *
     * @param string $foo
     * @return WebFrameworkUsers
     */
    public function setFoo($foo)
    {
        $this->foo = $foo;

        return $this;
    }

    /**
     * Get foo
     *
     * @return string 
     */
    public function getFoo()
    {
        return $this->foo;
    }

    /**
     * Set bar
     *
     * @param string $bar
     * @return WebFrameworkUsers
     */
    public function setBar($bar)
    {
        $this->bar = $bar;

        return $this;
    }

    /**
     * Get bar
     *
     * @return string 
     */
    public function getBar()
    {
        return $this->bar;
    }

    /**
     * Set etc
     *
     * @param string $etc
     * @return WebFrameworkUsers
     */
    public function setEtc($etc)
    {
        $this->etc = $etc;

        return $this;
    }

    /**
     * Get etc
     *
     * @return string 
     */
    public function getEtc()
    {
        return $this->etc;
    }
}
