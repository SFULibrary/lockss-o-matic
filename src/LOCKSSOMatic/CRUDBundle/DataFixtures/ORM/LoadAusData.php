<?php

namespace LOCKSSOMatic\CRUDBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CRUDBundle\Entity\Aus;

class LoadAusData extends AbstractFixture implements OrderedFixtureInterface
{
    
    public function getOrder()
    {
        return 2; // after plns and plugins
    }

    public function load(ObjectManager $manager)
    {
        $object = new Aus();
        $object->setAuid('uniq-au-id-1');
        $object->setManifestUrl('https://manifest.example.com/au1');
        $object->setOpen(1);
        $object->setPlugin($this->getReference('plugin-1'));
        $object->setPln($this->getReference('pln-1'));
        
        $manager->persist($object);
        $manager->flush();
        $this->setReference('au-1', $object);
    }
}
