<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test;

use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CoreBundle\Utilities\AbstractDataFixture;
use LOCKSSOMatic\CrudBundle\Entity\Au;

/**
 * Description of LoadAu
 *
 * @author mjoyce
 */
class LoadAu extends AbstractDataFixture
{
    public function getOrder() {
        return 3; 
    }
    
    protected function doLoad(ObjectManager $manager)
    {
        $au = new Au();
        $au->setComment('Test AU');
        $au->setContentprovider($this->referenceRepository->getReference('contentprovider'));
        $au->setPln($this->referenceRepository->getReference('pln'));
        $au->setPlugin($this->referenceRepository->getReference('plugin'));
        $manager->persist($au);
        $this->referenceRepository->addReference('au', $au);        
        $manager->flush();        
    }

    protected function getEnvironments()
    {
        return array('test');
    }
}
