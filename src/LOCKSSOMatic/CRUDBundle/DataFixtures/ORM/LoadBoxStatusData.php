<?php

namespace LOCKSSOMatic\CRUDBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CRUDBundle\Entity\BoxStatus;

class LoadBoxStatusData extends AbstractFixture implements OrderedFixtureInterface {
    
    public function getOrder()
    {
        return 3;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadStatus('up', 'yes', $this->getReference('box-1'), $manager);
        $this->loadStatus('pingable', 'yes', $this->getReference('box-1'), $manager);
        $this->loadStatus('acceptConnections', 'no', $this->getReference('box-1'), $manager);
        
        $this->loadStatus('up', 'no', $this->getReference('box-2'), $manager);
        $this->loadStatus('pingable', 'no', $this->getReference('box-2'), $manager);
        $this->loadStatus('acceptConnections', 'no', $this->getReference('box-2'), $manager);
    }

    private function loadStatus($key, $value, $box, $manager) {
        $object = new BoxStatus();
        $object->setBox($box);
        $object->setPropertyKey($key);
        $object->setPropertyValue($value);
        
        $manager->persist($object);
        $manager->flush();
    }
    
}