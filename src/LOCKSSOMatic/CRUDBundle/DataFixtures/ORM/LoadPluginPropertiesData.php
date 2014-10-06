<?php

namespace LOCKSSOMatic\CRUDBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CRUDBundle\Entity\PluginProperties;

class LoadPluginPropertiesData extends AbstractFixture implements OrderedFixtureInterface
{

    public function getOrder()
    {
        return 2;
    }

    public function load(ObjectManager $manager)
    {
        $object = new PluginProperties();
        $object->setPropertyKey('test');
        $object->setPropertyValue('yes');
        $object->setPlugin($this->getReference('plugin-1'));
        $manager->persist($object);
        $manager->flush();
        $this->setReference('pluginprop-1', $object);

        $object = new PluginProperties();
        $object->setPropertyKey('example');
        $object->setPropertyValue('no');
        $object->setPlugin($this->getReference('plugin-1'));
        $object->setParent($this->getReference('pluginprop-1'));
        $manager->persist($object);
        $manager->flush();

        $object = new PluginProperties();
        $object->setPropertyKey('impermanence');
        $object->setPropertyValue('oof that is a big word.');
        $object->setPlugin($this->getReference('plugin-1'));
        $object->setParent($this->getReference('pluginprop-1'));
        $manager->persist($object);
        $manager->flush();
    }

}
