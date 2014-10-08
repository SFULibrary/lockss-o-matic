<?php

namespace LOCKSSOMatic\CRUDBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CRUDBundle\Entity\Plns;

class LoadPlnsData extends AbstractFixture implements OrderedFixtureInterface
{
    
    public function getOrder()
    {
        return 1;
    }

    public function load(ObjectManager $manager)
    {
        $object = new Plns();
        $object->setName('Test PLN');
        $object->setPropsPath('/some/path/to/a/file');
        $manager->persist($object);
        $manager->flush();
        $this->setReference('pln-1', $object);
    }
}
