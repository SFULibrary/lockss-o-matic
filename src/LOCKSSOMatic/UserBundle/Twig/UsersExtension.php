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

namespace LOCKSSOMatic\UserBundle\Twig;

use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use LOCKSSOMatic\UserBundle\Entity\User;

/**
 * Wrapper around the user repository for use in twig templates. Lets you get a
 * list of users in any template. Configure this class as a service in
 * services.yml and then call it like this: {% for user in userList() %} ... {% endfor %}
 */
class UsersExtension extends \Twig_Extension
{

    /**
     * @var Doctrine
     */
    private $doctrine;

    public function __construct(Doctrine $doctrine = null)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Get a list of functions, keyed by function name.
     *
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'userList' => new \Twig_SimpleFunction('userList', array($this, 'userList')),
        );
    }

    /**
     * Get a list of users.
     *
     * @return User[]
     */
    public function userList()
    {
        $em = $this->doctrine->getManager();
        $users = $em->getRepository('LOCKSSOMaticUserBundle:User')->findAll();
        return $users;
    }

    /**
     * Get the extension's name
     *
     * @return string the name as configured in services.yml
     */
    public function getName()
    {
        return 'lom_usersextension';
    }
}
