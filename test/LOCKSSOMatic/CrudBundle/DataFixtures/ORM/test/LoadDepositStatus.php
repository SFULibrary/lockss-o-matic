<?php

namespace LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test;

use DateTime;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CoreBundle\Utilities\AbstractDataFixture;
use LOCKSSOMatic\CrudBundle\Entity\DepositStatus;

class LoadDepositStatus extends AbstractDataFixture {
    public function getOrder() {
        return 4; // After Deposit
    }
    
    protected function doLoad(ObjectManager $manager) {
        
        $s1 = new DepositStatus();
        $s1->setAgreement(0.75);
        $s1->setDeposit($this->referenceRepository->getReference('deposit.1'));
        $s1->setQueryDate(new DateTime('2016-01-01'));
        
        $status = array(1 => array (
            'expected' => '3c5a0a0363cedb7',
            'lockss1' => '3c5a0a0363cedb7',
            'lockssa' => '3c5a0a0363cedb7',
            'lockss2' => '*',
            'lockssb' => '3C5A0A0363CEDB7',
        ));
        $s1->setStatus($status);
        
        $manager->persist($s1);
        $this->referenceRepository->setReference('depositstatus.1', $s1);        

        $manager->flush();
    }

    protected function getEnvironments() {
        return array('test', 'dev');
    }
}