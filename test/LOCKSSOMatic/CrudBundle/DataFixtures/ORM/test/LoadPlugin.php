<?php

namespace LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test;

use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CoreBundle\Utilities\AbstractDataFixture;
use LOCKSSOMatic\CrudBundle\Entity\Plugin;

class LoadPlugin extends AbstractDataFixture {
    public function getOrder() {
        return 1;
    }
    
    protected function doLoad(ObjectManager $manager) {
        $plugin = new Plugin();
        $plugin->setFilename("TestPlugin.jar");
        $plugin->setIdentifier("ca.sfu.plugin.test");
        $plugin->setName("Test Plugin");
        $plugin->setVersion(6);
        $plugin->setPath('/path/to/TestPlugin-v6.jar');
        $manager->persist($plugin);
        $manager->flush();
        $this->referenceRepository->setReference('plugin', $plugin);        
    }

    protected function getEnvironments() {
        return array('test');
    }
}