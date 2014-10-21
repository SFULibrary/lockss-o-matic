<?php

namespace LOCKSSOMatic\CRUDBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CRUDBundle\Entity\ContentProviders;

class LoadContentProvidersData extends AbstractFixture implements OrderedFixtureInterface
{
    
    public function getOrder()
    {
        return 2;
    }

    public function load(ObjectManager $manager)
    {
        $object = new ContentProviders();
        $object->setType('application');
        $object->setUuid('f705d95b-7a0a-451c-a51e-242bc4ab53cf');
        $object->setPermissionUrl('http://provider.example.com/path/to/permission/stmt');
        $object->setName('Example Provider Inc.');
        $object->setIpAddress('192.168.100.200');
        $object->setHostname('cp1.example.com');
        $object->setChecksumType('md5');
        // maxFileSize is in kB (1,000 bytes).
        $object->setMaxFileSize('123456');
        $object->setMaxAuSize('987654321');
        $object->setContentOwner($this->getReference('owner-1'));
        $object->setPln($this->getReference('pln-1'));
        
        $manager->persist($object);
        $manager->flush();
        $this->setReference('cp-1', $object);
    }
}
