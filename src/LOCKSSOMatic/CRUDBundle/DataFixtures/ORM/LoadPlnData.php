<?php

namespace LOCKSSOMatic\CRUDBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CRUDBundle\Entity\Plns;

class LoadPlnData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $pln = new Plns();
        $pln->setName('Test Pln');
        $pln->addContentProvider($this->getReference('cp-1'));
        $this->setReference('network-1', $pln);
        
        $manager->persist($pln);
        $manager->flush();
    }

    /**
     * Plns must be loaded after ContentProviders.
     *
     * @return int
     */
    public function getOrder()
    {
        return 3;
    }
}
