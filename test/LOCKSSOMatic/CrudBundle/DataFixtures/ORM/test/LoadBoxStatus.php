<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test;

use DateTime;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CoreBundle\Utilities\AbstractDataFixture;
use LOCKSSOMatic\CrudBundle\Entity\BoxStatus;

/** 
 * Description of LoadAu
 *
 * @author mjoyce
 */
class LoadBoxStatus extends AbstractDataFixture
{
    public function getOrder() {
        return 3; // After Box
    }
    
    protected function doLoad(ObjectManager $manager)
    {
        $b1 = new BoxStatus();
        $b1->setBox($this->referenceRepository->getReference('box.1'));
        $b1->setQueryDate(new DateTime());
        $b1->setSuccess(true);
        $b1->setStatus(array(
            'activeCount' => 23,
            'free' => 123,
            'size' => 432,
            'used' => 309,
        ));
        $manager->persist($b1);
        $this->referenceRepository->setReference('boxstatus.1', $b1);
        
        $b2 = new BoxStatus();
        $b2->setBox($this->referenceRepository->getReference('box.1'));
        $b2->setQueryDate(new DateTime());
        $b2->setSuccess(true);
        $b2->setStatus(array(
            'activeCount' => 3,
            'free' => 13,
            'size' => 32,
            'used' => 29,
        ));
        $manager->persist($b2);
        $this->referenceRepository->setReference('boxstatus.2', $b2);
        $manager->flush();        
    }

    protected function getEnvironments()
    {
        return array('test');
    }

//put your code here
}
