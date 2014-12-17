<?php

/* 
 * The MIT License
 *
 * Copyright 2014. Michael Joyce <ubermichael@gmail.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace LOCKSSOMatic\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\Common\Collections\Collection;
use LOCKSSOMatic\LoggingBundle\Entity\LogEntry;

/**
 * User entity. Extends FOSUserBundle to get all the goodness. We want usernames
 * to be email addresses, but the base bundle doesn't handle that out of the box.
 *
 * To acheive this, AdminUserType and ProfileFormType both add an event listener
 * which sets the username field to the email address field.
 *
 * @ORM\Table(name="fos_user")
 * @ORM\Entity(repositoryClass="LOCKSSOMatic\UserBundle\Entity\UserRepository")
 */
class User extends BaseUser
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="fullname", type="string", length=128)
     */
    private $fullname;

    /**
     * @var string
     *
     * @ORM\Column(name="institution", type="string", length=128)
     */
    private $institution;
    
    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="LOCKSSOMatic\LoggingBundle\Entity\LogEntry", mappedBy="user")
     */
    private $logs;

    public function __construct()
    {
        parent::__construct();
        $this->logs = new Collection();
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
     * Set fullname
     *
     * @param string $fullname
     * @return User
     */
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;

        return $this;
    }

    /**
     * Get fullname
     *
     * @return string
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * Set institution
     *
     * @param string $institution
     * @return User
     */
    public function setInstitution($institution)
    {
        $this->institution = $institution;

        return $this;
    }

    /**
     * Get institution
     *
     * @return string
     */
    public function getInstitution()
    {
        return $this->institution;
    }

    /**
     * Add logs
     *
     * @param LogEntry $logs
     * @return Plns
     */
    public function addLog(LogEntry $logs)
    {
        $this->logs[] = $logs;

        return $this;
    }

    /**
     * Remove logs
     *
     * @param LogEntry $logs
     */
    public function removeLog(LogEntry $logs)
    {
        $this->logs->removeElement($logs);
    }

    /**
     * Get logs
     *
     * @return Collection 
     */
    public function getLogs()
    {
        return $this->logs;
    }
}
