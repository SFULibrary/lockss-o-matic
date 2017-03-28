<?php

namespace LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test;

use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CoreBundle\Utilities\AbstractDataFixture;
use LOCKSSOMatic\CrudBundle\Entity\ContentOwner;

class LoadContentOwner extends AbstractDataFixture {
    public function getOrder() {
        return 1;
    }
    
    protected function doLoad(ObjectManager $manager) {
        $owner = new ContentOwner();
        $owner->setName('Test Owner');
        $owner->setEmailAddress('test@example.com');
        $manager->persist($owner);
        $manager->flush();
        $this->referenceRepository->setReference('contentowner', $owner);        
    }

    protected function getEnvironments() {
        return array('test', 'dev');
    }
}