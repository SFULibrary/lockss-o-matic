<?php

namespace LOCKSSOMatic\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\UserBundle\Entity\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface {
    
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
        return 3;
    }
    
}