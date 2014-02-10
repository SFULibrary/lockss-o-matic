<?php

namespace LOCKSSOMatic\CRUDBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CRUDBundle\Entity\ContentProviders;

class LoadContentProvidersData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $contentProvider = new ContentProviders();
        $contentProvider->setContentOwnersId('1');
        $contentProvider->setType('application');
        $contentProvider->setName('Test application');
        $contentProvider->setIpAddress('111.222.333.444');

        $manager->persist($contentProvider);
        $manager->flush();
    }
}
