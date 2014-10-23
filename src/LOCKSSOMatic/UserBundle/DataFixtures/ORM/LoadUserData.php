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

namespace LOCKSSOMatic\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\UserBundle\Entity\User;

/**
 * Load user data fixtures.
 */
class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * Users to create.
     *
     * @var array
     */
    private $userdata = array(
        array('franklin-admin', 'franklin.admin@example.com', 'franklin Admin', 'ROLE_LOMADMIN'),
        array('franklin-depositor', 'franklin.depositor@example.com', 'franklin depositor', null),
        array('franklin-monitor', 'franklin.monitor@example.com', 'franklin monitor', null),
        array('dewey-admin', 'dewey.admin@example.com', 'dewey Admin', 'ROLE_LOMADMIN'),
        array('dewey-depositor', 'dewey.depositor@example.com', 'dewey depositor', 'ROLE_LOMADMIN'),
        array('dewey-monitor', 'dewey.monitor@example.com', 'dewey monitor', null),
        array('shared-admin', 'shared.admin@example.com', 'shared Admin', 'ROLE_LOMADMIN'),
        array('shared-depositor', 'shared.depositor@example.com', 'shared depositor', null),
        array('shared-monitor', 'shared.monitor@example.com', 'shared monitor', null),
        array('user-noaccess', 'user@example.com', 'User without access', null),
    );

    /**
     * Load the user fixtures.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $admin = $this->buildUser($manager, 'admin@example.com', 'Site admin', 'ROLE_ADMIN');
        $this->setReference('admin-user', $admin);
        
        foreach ($this->userdata as $d) {
            $user = $this->buildUser($manager, $d[1], $d[2], $d[3]);
            $this->setReference($d[0], $user);
        }

        $manager->flush();
    }

    /**
     * Build a new user and return it.
     *
     * @param string $username the user name
     * @param string $fullname the full name
     * @param string $role     the role, as remembered in LoadRoleData
     *
     * @return User
     */
    private function buildUser($manager, $username, $fullname, $role = null)
    {
        $user = new User();
        // AdminUserType sets the username to the email address, so do that here as well.
        $user->setUsername($username);
        $user->setEmail($username);
        $user->setFullname($fullname);
        $user->setInstitution("");
        $user->setEnabled(true);
        $user->setLocked(false);
        
        if ($role !== null) {
            $user->addRole($role);
        }
        $user->setPlainPassword('supersecret');
        $manager->persist($user);

        return $user;
    }

    /**
     * Users must be loaded before ACLs.
     *
     * @return int
     */
    public function getOrder()
    {
        return 1;
    }
}
