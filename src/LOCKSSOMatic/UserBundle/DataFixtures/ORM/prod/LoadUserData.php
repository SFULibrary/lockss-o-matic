<?php

namespace LOCKSSOMatic\UserBundle\DataFixtures\ORM\prod;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CoreBundle\Utilities\AbstractDataFixture;
use LOCKSSOMatic\UserBundle\Entity\User;

class LoadUserData extends AbstractDataFixture implements OrderedFixtureInterface {
    
    /**
     * Load the user fixtures.
     *
     * @param ObjectManager $manager
     */
    public function doLoad(ObjectManager $manager)
    {
        $admin = $this->buildUser($manager, 'admin@example.com', 'Site admin', 'ROLE_ADMIN');
        $this->setReference('admin-user', $admin);
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

    protected function getEnvironments()
    {
        return array('prod');
    }

}