<?php

namespace LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test;

use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CoreBundle\Utilities\AbstractDataFixture;
use LOCKSSOMatic\CrudBundle\Entity\Deposit;

class LoadExtraDeposit extends AbstractDataFixture {
    public function getOrder() {
        return 4; // After Deposit
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

        $manager->flush();
    }

    protected function getEnvironments() {
        return array('test');
    }
}