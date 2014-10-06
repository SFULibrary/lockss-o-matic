<?php

namespace LOCKSSOMatic\CRUDBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CRUDBundle\Entity\AuProperties;

class LoadAuPropertyData extends AbstractFixture implements OrderedFixtureInterface {
    
    public function getOrder()
    {
        return 3;
    }

    public function load(ObjectManager $manager)
    {
        $object = new AuProperties();
        $object->setPropertyKey('test');
        $object->setPropertyValue('yes');
        $object->setAu($this->getReference('au-1'));
        $manager->persist($object);
        $manager->flush();
        $this->setReference('auprop-1', $object);
        
        $object = new AuProperties();
        $object->setPropertyKey('example');
        $object->setPropertyValue('no');
        $object->setAu($this->getReference('au-1'));
        $object->setParent($this->getReference('auprop-1'));        
        $manager->persist($object);
        $manager->flush();

        $object = new AuProperties();
        $object->setPropertyKey('recursive');
        $object->setPropertyValue('P. 269: recursion  86, 139, 141, 182, 202, 269');
        $object->setAu($this->getReference('au-1'));
        $object->setParent($this->getReference('auprop-1'));        
        $manager->persist($object);
        $manager->flush();
    }

}