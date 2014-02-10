<?php

namespace LOCKSSOMatic\CRUDBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CRUDBundle\Entity\Aus;

class LoadAusData implements FixtureInterface
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
}
