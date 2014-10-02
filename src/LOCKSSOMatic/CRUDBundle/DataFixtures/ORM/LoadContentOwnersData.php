<?php

namespace LOCKSSOMatic\CRUDBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CRUDBundle\Entity\ContentOwners;

class LoadContentOwnersData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $owner = new ContentOwners();
        $owner->setName('Test Content Owner');
        $owner->setEmailAddress('admin@foo.org');
        $this->setReference('owner-1', $owner);
        
        $manager->persist($owner);
        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }

}
