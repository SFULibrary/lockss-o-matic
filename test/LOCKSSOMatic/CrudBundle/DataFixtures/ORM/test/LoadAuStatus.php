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
use LOCKSSOMatic\CrudBundle\Entity\AuStatus;

/** 
 * Description of LoadAu
 *
 * @author mjoyce
 */
class LoadAuStatus extends AbstractDataFixture
{
    public function getOrder() {
        return 4; // After Au and Box.
    }
     
    protected function doLoad(ObjectManager $manager)
    {
        $s1 = new AuStatus();
        $s1->setAu($this->referenceRepository->getReference('au'));
        $s1->setBox($this->referenceRepository->getReference('box.1'));
        $s1->setQueryDate(new DateTime());
        $s1->setStatus(array(
            'status' => 'ok', 
            'contentSize' => 123,
            'diskUsage' => 432,
            'lastPoll' => '2012-10-10',
            'lastCrawlResult' => 'success',
            'lastPollResult' => 'agreement',
        ));
        $manager->persist($s1);        
        $this->referenceRepository->setReference('austatus.1', $s1);
        
        $s2 = new AuStatus();
        $s2->setAu($this->referenceRepository->getReference('au'));
        $s2->setBox($this->referenceRepository->getReference('box.2'));
        $s2->setQueryDate(new DateTime());
        $s2->setStatus(array('status' => 'ok', 'key' => 'value', 'size' => 2234));
        $manager->persist($s2);
        $this->referenceRepository->setReference('austatus.2', $s2);
        $manager->flush();        
    }

    protected function getEnvironments()
    {
        return array('test');
    }

//put your code here
}
