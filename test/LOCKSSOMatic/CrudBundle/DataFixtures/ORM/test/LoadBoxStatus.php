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
        $s1 = new BoxStatus();
        $s1->setSuccess(false);
        $s1->setErrors('Errors occured.');
        $s1->setQueryDate(new DateTime('2016-01-01'));
        $s1->setBox($this->getReference('box.1'));
        $manager->persist($s1);
        $this->referenceRepository->setReference('box_status.1', $s1);
        
        $manager->flush();        
    }

    protected function getEnvironments()
    {
        return array('test', 'dev');
    }

//put your code here
}
