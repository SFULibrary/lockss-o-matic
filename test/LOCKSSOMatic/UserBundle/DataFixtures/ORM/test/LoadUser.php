<?php

namespace LOCKSSOMatic\UserBundle\DataFixtures\ORM\test;

use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CoreBundle\Utilities\AbstractDataFixture;
use LOCKSSOMatic\UserBundle\Entity\User;

class LoadUser extends AbstractDataFixture
{
    
    public function doLoad(ObjectManager $manager)
    {
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setPlainPassword('supersecret');
        $admin->setFullname('Admin User');
        $admin->setInstitution('Test Inst');
        $admin->setEnabled(true);
        $admin->addRole('ROLE_ADMIN');
        $manager->persist($admin);
        $this->setReference('user.admin', $admin);
        
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setPlainPassword('supersecret');
        $user->setFullname('Norm User');
        $user->setInstitution('Test Inst');
        $user->setEnabled(true);
        $user->addRole('ROLE_USER');
        $manager->persist($user);
        $this->setReference('user.normal', $user);
        $manager->flush();
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

    protected function getEnvironments()
    {
        return array('test', 'dev');
    }
}
