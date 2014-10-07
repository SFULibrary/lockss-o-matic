<?php

namespace LOCKSSOMatic\CRUDBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CRUDBundle\Entity\ContentProviders;

class LoadContentProvidersData extends AbstractFixture implements OrderedFixtureInterface
{
    
    public function getOrder()
    {
        return 2;
    }

    public function load(ObjectManager $manager)
    {
        $object = new ContentProviders();
        $object->setType('application');
        $object->setName('Example Provider Inc.');
        $object->setIpAddress('192.168.100.200');
        $object->setHostname('cp1.example.com');
        $object->setChecksumType('sha1');
        
        $manager->persist($object);
        $manager->flush();
        $this->setReference('cp-1', $object);
    }

}
