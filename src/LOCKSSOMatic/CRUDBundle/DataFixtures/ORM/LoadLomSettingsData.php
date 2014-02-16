<?php

namespace LOCKSSOMatic\CRUDBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CRUDBundle\Entity\LomSettings;

class LoadLomSettingsData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $lom = new LomSettings();
        $lom->setSiteName('Example LOM instance');
        $lom->setBaseUrl('http://localhost/lockss-o-matic');
        $lom->setIpAddress('127.0.0.1');
        $lom->setPathToUploads('/var/www/lockss-o-matic/uploads');
        $lom->setPathToPlnFiles('/var/www/lockss-o-matic/plnfiles');

        $manager->persist($lom);
        $manager->flush();
    }
}
