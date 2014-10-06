<?php

namespace LOCKSSOMatic\CRUDBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CRUDBundle\Entity\Boxes;

class LoadBoxesData extends AbstractFixture implements OrderedFixtureInterface {
    
    public function getOrder()
    {
        return 2;
    }

    public function load(ObjectManager $manager)
    {
        $object = new Boxes();
        $object->setHostname('box1.example.com');
        $object->setIpAddress('192.168.0.0');
        $object->setUsername('userb1');
        $object->setPassword('somegreatsecret');
        $object->setPln($this->getReference('pln-1'));
        
        $manager->persist($object);
        $manager->flush();
        $this->setReference('box-1', $object);

        $object = new Boxes();
        $object->setHostname('box2.example.com');
        $object->setIpAddress('192.168.0.1');
        $object->setUsername('userb2');
        $object->setPassword('ranchdressing');
        $object->setPln($this->getReference('pln-1'));

        $manager->persist($object);
        $manager->flush();
        $this->setReference('box-2', $object);
    }

}