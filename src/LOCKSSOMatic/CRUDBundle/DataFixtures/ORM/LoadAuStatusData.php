<?php

namespace LOCKSSOMatic\CRUDBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CRUDBundle\Entity\AuStatus;

class LoadAuStatusData extends AbstractFixture implements OrderedFixtureInterface
{

    public function getOrder()
    {
        return 3;
    }

    public function load(ObjectManager $manager)
    {
        $object = new AuStatus();
        $object->setBoxHostname('box3.example.com');
        $object->setPropertyKey('inprogress');
        $object->setPropertyValue('yes');
        $object->setAu($this->getReference('au-1'));
        $manager->persist($object);
        $manager->flush();

        $object = new AuStatus();
        $object->setBoxHostname('box1.example.com');
        $object->setPropertyKey('complete');
        $object->setPropertyValue('yes');
        $object->setAu($this->getReference('au-1'));
        $manager->persist($object);
        $manager->flush();

        $object = new AuStatus();
        $object->setBoxHostname('box1.example.com');
        $object->setPropertyKey('colour');
        $object->setPropertyValue('Canadian winterberry');
        $object->setAu($this->getReference('au-1'));
        $manager->persist($object);
        $manager->flush();
    }
}
