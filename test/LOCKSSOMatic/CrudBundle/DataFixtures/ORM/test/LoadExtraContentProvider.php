<?php

namespace LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test;

use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CoreBundle\Utilities\AbstractDataFixture;
use LOCKSSOMatic\CrudBundle\Entity\ContentProvider;

class LoadExtraContentProvider extends AbstractDataFixture {
    public function getOrder() {
        return 3; // After ContentProvider
    }
    
    protected function doLoad(ObjectManager $manager) {
        $provider = new ContentProvider();
        $provider->setContentOwner($this->referenceRepository->getReference('contentowner'));
        $provider->setMaxAuSize(10000);
        $provider->setMaxFileSize(1000);
        $provider->setName("Test Provider");
        $provider->setPermissionurl("http://example.com/permission");
        $provider->setPln($this->referenceRepository->getReference('pln.2'));
        $provider->setPlugin($this->referenceRepository->getReference('plugin'));
        $provider->setUuid('79A77790-5C54-4234-8555-B7093FBE7414');
        $manager->persist($provider);
        $manager->flush();
        $this->referenceRepository->setReference('contentprovider.2', $provider);        
    }

    protected function getEnvironments() {
        return array('test', 'dev');
    }
}