<?php

namespace LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test;

use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CoreBundle\Utilities\AbstractDataFixture;
use LOCKSSOMatic\CrudBundle\Entity\Pln;

/**
 * Description of LoadPln
 *
 * @author mjoyce
 */
class LoadPln extends AbstractDataFixture 
{
    protected function doLoad(ObjectManager $manager)
    {
        $pln = new Pln();
        $pln->setName('T1');
        $manager->persist($pln);
        $manager->flush();
        $this->referenceRepository->addReference('pln', $pln);
    }

    protected function getEnvironments()
    {
        return array('test');
    }

//put your code here
}
