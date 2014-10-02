<?php

namespace LOCKSSOMatic\CRUDBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CRUDBundle\Entity\Aus;

class LoadAusData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $au = new Aus();
        $au->setPlnsId('1');
        $au->setAuid('TestAuId');
        $au->setManifestUrl('http://foo.example.com/manifest.htm');

        $manager->persist($au);
        $manager->flush();
    }

    public function getOrder()
    {
        return 4;
    }

}
