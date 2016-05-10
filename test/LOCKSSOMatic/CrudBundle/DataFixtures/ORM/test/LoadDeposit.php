<?php

namespace LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test;

use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CoreBundle\Utilities\AbstractDataFixture;
use LOCKSSOMatic\CrudBundle\Entity\Deposit;

class LoadDeposit extends AbstractDataFixture {
    public function getOrder() {
        return 3; // After ContentProvider
    }
    
    protected function doLoad(ObjectManager $manager) {
        $deposit = new Deposit();
        $deposit->setContentProvider($this->referenceRepository->getReference('contentprovider'));
        $deposit->setSummary('Test deposits');
        $deposit->setTitle('Tests');
        $deposit->setUuid('F4768EB8-ABFD-429E-9757-F069A11702EE');
        $deposit->setDepositDate();
        $manager->persist($deposit);
        $manager->flush();
        $this->referenceRepository->setReference('deposit', $deposit);        
    }

    protected function getEnvironments() {
        return array('test');
    }

}