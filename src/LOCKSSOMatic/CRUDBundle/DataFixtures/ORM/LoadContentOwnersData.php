<?php

namespace LOCKSSOMatic\CRUDBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CRUDBundle\Entity\ContentOwners;

class LoadContentOwnersData extends AbstractFixture implements OrderedFixtureInterface
{
    
    public function getOrder()
    {
        return 1; 
    }

    public function load(ObjectManager $manager)
    {
        $object = new ContentOwners();
        $object->setEmailAddress('owner@example.com');
        $object->setName('Example Owner, Dr.');
        
        $manager->persist($object);
        $manager->flush();
        $this->setReference('owner-1', $object);
    }

}
