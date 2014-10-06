<?php

namespace LOCKSSOMatic\CRUDBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CRUDBundle\Entity\ExternalTitleDbs;

class LoadExternalTitleDbsData extends AbstractFixture implements OrderedFixtureInterface
{
    
    public function getOrder()
    {
        return 2;
    }

    public function load(ObjectManager $manager)
    {
        $object = new ExternalTitleDbs();
        $object->setPath('/some/example/path');
        $object->setPln($this->getReference('pln-1'));
        $manager->persist($object);
        $manager->flush();
        $this->setReference('ext-1', $object);
    }
}
