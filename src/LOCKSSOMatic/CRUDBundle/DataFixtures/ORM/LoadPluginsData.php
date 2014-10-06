<?php

namespace LOCKSSOMatic\CRUDBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CRUDBundle\Entity\Plugins;

class LoadPluginsData extends AbstractFixture implements OrderedFixtureInterface
{
    
    public function getOrder()
    {
        return 1;
    }

    public function load(ObjectManager $manager)
    {
        $object = new Plugins();
        $object->setName('Test Plugin');
        $manager->persist($object);
        $manager->flush();
        $this->setReference('plugin-1', $object);
    }
}
