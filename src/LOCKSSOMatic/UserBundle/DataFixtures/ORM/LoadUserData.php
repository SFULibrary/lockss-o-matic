<?php

namespace LOCKSSOMatic\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\UserBundle\Entity\User;

class LoadUserData implements FixtureInterface {
    
    public function load(ObjectManager $em) {
        $adminUser = new User();
        $adminUser->setEmail("admin@example.com");
        $adminUser->setUsername("admin@example.com");
        $adminUser->setFullname("Admin User");
        $adminUser->setInstitution("Test Institution");
        $adminUser->setPlainPassword("supersecret");
        $adminUser->setEnabled(true);
        $adminUser->addRole("ROLE_ADMIN");
        $adminUser->addRole("ROLE_SUPER_ADMIN");
        
        $em->persist($adminUser);
        $em->flush();
    }
    
}