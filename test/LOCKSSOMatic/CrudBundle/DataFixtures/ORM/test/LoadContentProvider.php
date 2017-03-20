<?php

namespace LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test;

use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CoreBundle\Utilities\AbstractDataFixture;
use LOCKSSOMatic\CrudBundle\Entity\ContentProvider;

class LoadContentProvider extends AbstractDataFixture {
    public function getOrder() {
        return 2; // After ContentOwner, Pln, Plugin.
    }
    
    protected function doLoad(ObjectManager $manager) {
        $provider = new ContentProvider();
        $provider->setContentOwner($this->referenceRepository->getReference('contentowner'));
        $provider->setMaxAuSize(10000);
        $provider->setMaxFileSize(1000);
        $provider->setName("Test Provider");
        $provider->setPermissionurl("http://example.com/permission");
        $provider->setPln($this->referenceRepository->getReference('pln'));
        $provider->setPlugin($this->referenceRepository->getReference('plugin'));
        $provider->setUuid('49EC670D-A5C9-42E1-8054-AC64A17B6475');
        $manager->persist($provider);
        $manager->flush();
        $this->referenceRepository->setReference('contentprovider', $provider);        
    }

    protected function getEnvironments() {
        return array('test', 'dev');
    }
}