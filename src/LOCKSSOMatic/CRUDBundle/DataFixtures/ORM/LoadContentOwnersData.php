<?php

namespace LOCKSSOMatic\CRUDBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CRUDBundle\Entity\ContentOwners;

class LoadContentOwnersData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $owner = new ContentOwners();
        $owner->setName('Test Content Owner');
        $owner->setEmailAddress('admin@foo.org');

        $manager->persist($owner);
        $manager->flush();
    }
}
