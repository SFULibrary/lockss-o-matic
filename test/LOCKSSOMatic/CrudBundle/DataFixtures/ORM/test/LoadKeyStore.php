<?php

namespace LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test;

use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CoreBundle\Utilities\AbstractDataFixture;
use LOCKSSOMatic\CrudBundle\Entity\Keystore;

class LoadKeyStore extends AbstractDataFixture {
    public function getOrder() {
        return 2; // After Pln
    }
    
    protected function doLoad(ObjectManager $manager) {
        $pln = $this->referenceRepository->getReference('pln');
        
        $k1 = new Keystore();
        $k1->setFilename('test.keystore');
        $k1->setPln($pln);
        $k1->setPath('/tmp/path/test.keystore');
        $pln->setKeystore($k1);

        $manager->persist($k1);
        $this->referenceRepository->setReference('keystore', $k1);        

        $manager->flush();
    }

    protected function getEnvironments() {
        return array('test');
    }
}