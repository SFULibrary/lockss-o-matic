<?php

namespace LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test;

use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CoreBundle\Utilities\AbstractDataFixture;
use LOCKSSOMatic\CrudBundle\Entity\Deposit;

class LoadExtraDeposit extends AbstractDataFixture {
    public function getOrder() {
        return 3; // After ContentProvider
    }
    
    protected function doLoad(ObjectManager $manager) {
        $d1 = new Deposit();
        $d1->setContentProvider($this->referenceRepository->getReference('contentprovider.2'));
        $d1->setSummary('Test deposits');
        $d1->setTitle('Tests');
        $d1->setUuid('5E2E042F-ECFD-4B00-85F9-B5172E082C07');
        $d1->setDepositDate();
        $manager->persist($d1);
        $this->referenceRepository->setReference('deposit.3', $d1);        

        $d2 = new Deposit();
        $d2->setContentProvider($this->referenceRepository->getReference('contentprovider.2'));
        $d2->setSummary('Test deposit 2');
        $d2->setTitle('Tests More');
        $d2->setUuid('F39AA52A-C5AB-47E7-9CDD-34921D4F953C');
        $d2->setDepositDate();
        $manager->persist($d2);
        $this->referenceRepository->setReference('deposit.4', $d2);        
        
        $manager->flush();
    }

    protected function getEnvironments() {
        return array('test');
    }
}