<?php

namespace LOCKSSOMatic\CRUDBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CRUDBundle\Entity\ContentProviders;

class LoadContentProvidersData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $contentProvider = new ContentProviders();
        $contentProvider->setContentOwner($this->getReference('owner-1'));
        $contentProvider->setType('application');
        $contentProvider->setName('Test application');
        $contentProvider->setIpAddress('111.222.333.444');
        $contentProvider->setHostname('foo.example.com');
        $contentProvider->setChecksumType('md5');
        $contentProvider->setMaxFileSize('8388608');
        $contentProvider->setMaxAuSize('8388608');

        $this->setReference('provider-1', $contentProvider);
        
        $manager->persist($contentProvider);
        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }

}
