<?php

namespace SFU\SampleData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\UserBundle\Entity\User;

/**
 * Description of LoadUsers
 *
 * @author mjoyce
 */
class LoadUsers  extends AbstractFixture implements OrderedFixtureInterface {
    
    
    public function getOrder() {
        return 1;
    }

    public function load(ObjectManager $manager) {
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setPlainPassword('supersecret');
        $admin->setFullname('Admin User');
        $admin->setInstitution('Test Inst');
        $admin->setEnabled(true);
        $admin->addRole('ROLE_SUPER_ADMIN');
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

}
