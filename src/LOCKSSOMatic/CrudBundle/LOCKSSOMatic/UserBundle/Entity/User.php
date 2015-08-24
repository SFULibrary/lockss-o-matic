<?php

namespace LOCKSSOMatic\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="lom_user")
 * @ORM\Entity(repositoryClass="LOCKSSOMatic\UserBundle\Entity\UserRepository")
 */
class User
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="fullname", type="string", length=128, precision=0, scale=0, nullable=false, unique=false)
     */
    private $fullname;

    /**
     * @var string
     *
     * @ORM\Column(name="institution", type="string", length=128, precision=0, scale=0, nullable=false, unique=false)
     */
    private $institution;


}
