<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test;

use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CoreBundle\Utilities\AbstractDataFixture;
use LOCKSSOMatic\CrudBundle\Entity\Box;

/** 
 * Description of LoadAu
 *
 * @author mjoyce
 */
class LoadBox extends AbstractDataFixture
{
    public function getOrder() {
        return 2; // After Pln 
    }
    
    protected function doLoad(ObjectManager $manager)
    {
        $b1 = new Box();
        $b1->setHostname('localhost');
        $b1->setIpAddress('127.0.0.1');
        $b1->setPln($this->referenceRepository->getReference('pln'));
        $b1->setPort('8088');
        $b1->setProtocol('TCP');
        $b1->setWebServicePort('8089');
        $manager->persist($b1);
        $this->referenceRepository->setReference('box.1', $b1);
        
        $b2 = new Box();
        $b2->setHostname('localhost');
        $b2->setIpAddress('127.0.0.1');
        $b2->setPln($this->referenceRepository->getReference('pln'));
        $b2->setPort('9098');
        $b2->setProtocol('TCP');
        $b2->setWebServicePort('9099');
        $manager->persist($b2);
        $this->referenceRepository->setReference('box.2', $b2);
        $manager->flush();        
    }

    protected function getEnvironments()
    {
        return array('test');
    }

//put your code here
}
