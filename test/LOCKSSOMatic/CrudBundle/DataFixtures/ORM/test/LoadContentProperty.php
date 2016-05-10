<?php

namespace LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test;

use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CoreBundle\Utilities\AbstractDataFixture;
use LOCKSSOMatic\CrudBundle\Entity\ContentProperty;

class LoadContentProperty extends AbstractDataFixture {
    public function getOrder() {
        return 5;
    }
    
    protected function doLoad(ObjectManager $manager) {
        $p1 = new ContentProperty();
        $p1->setContent($this->referenceRepository->getReference('content.1'));
        $p1->setPropertyKey('prop1');
        $p1->setPropertyValue('http://example.com');
        $manager->persist($p1);        
        $this->referenceRepository->setReference('contentproperty.1', $p1);        
        
        $p2 = new ContentProperty();
        $p2->setContent($this->referenceRepository->getReference('content.1'));
        $p2->setPropertyKey('prop2');
        $p2->setPropertyValue(array('abc/', 'pdq'));
        $manager->persist($p2);        
        $this->referenceRepository->setReference('contentproperty.2', $p2);        
        $manager->flush();
    }

    protected function getEnvironments() {
        return array('test');
    }
}