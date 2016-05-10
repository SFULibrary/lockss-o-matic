<?php

namespace LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test;

use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CoreBundle\Utilities\AbstractDataFixture;
use LOCKSSOMatic\CrudBundle\Entity\Pln;

class LoadPln extends AbstractDataFixture {
    public function getOrder() {
        return 1;
    }
    
    protected function doLoad(ObjectManager $manager) {
        $pln = new Pln();
        $pln->setDescription("Test PLN for testing.");
        $pln->setName("TestPln");
        $pln->setPassword('abc123');
        $pln->setUsername('testuser');
        $pln->setProperty("ca.sfu.test.list", array("item 1", "item 2", "item 3"));
        $pln->setProperty("ca.sfu.test.value", "single value");
        $manager->persist($pln);
        $manager->flush();
        $this->referenceRepository->setReference('pln', $pln);        
    }

    protected function getEnvironments() {
        return array('test');
    }
}