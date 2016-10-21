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
use LOCKSSOMatic\CrudBundle\Entity\CacheStatus;

/** 
 * Description of LoadAu
 *
 * @author mjoyce
 */
class LoadCacheStatus extends AbstractDataFixture
{
    public function getOrder() {
        return 4; // After BoxStatus
    }
    
    protected function doLoad(ObjectManager $manager)
    {
        $c1 = new CacheStatus();
        $c1->setBoxStatus($this->getReference('box_status.1'));
        $c1->setResponse(array(
            'foo' => 'bar',
            'number' => 33,
            'repositorySpaceId' => 'chili/cheese/fries'
        ));
        $manager->persist($c1);
        $this->referenceRepository->setReference('cache_status.1', $c1);
        
        $manager->flush();        
    }

    protected function getEnvironments()
    {
        return array('test');
    }

//put your code here
}
