<?php

namespace LOCKSSOMatic\CRUDBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CRUDBundle\Entity\PlnProperties;

class LoadPlnPropertiesData extends AbstractFixture implements OrderedFixtureInterface
{

    public function getOrder()
    {
        return 2;
    }

    public function load(ObjectManager $manager)
    {
        $object = new PlnProperties();
        $object->setPropertyKey('test');
        $object->setPropertyValue('yes');
        $object->setPln($this->getReference('pln-1'));
        $manager->persist($object);
        $manager->flush();
        $this->setReference('plnprop-1', $object);
        
        $object = new PlnProperties();
        $object->setPropertyKey('example');
        $object->setPropertyValue('no');
        $object->setPln($this->getReference('pln-1'));
        $object->setParent($this->getReference('plnprop-1'));
        $manager->persist($object);
        $manager->flush();

        $object = new PlnProperties();
        $object->setPropertyKey('recursive');
        $object->setPropertyValue('P. 269: recursion  86, 139, 141, 182, 202, 269');
        $object->setPln($this->getReference('pln-1'));
        $object->setParent($this->getReference('plnprop-1'));
        $manager->persist($object);
        $manager->flush();
    }
}
