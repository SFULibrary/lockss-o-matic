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
        $d1 = new Deposit();
        $d1->setContentProvider($this->referenceRepository->getReference('contentprovider'));
        $d1->setSummary('Test deposits');
        $d1->setTitle('Tests');
        $d1->setUuid('F4768EB8-ABFD-429E-9757-F069A11702EE');
        $d1->setDepositDate();
        $manager->persist($d1);
        $this->referenceRepository->setReference('deposit.1', $d1);        

        $d2 = new Deposit();
        $d2->setContentProvider($this->referenceRepository->getReference('contentprovider'));
        $d2->setSummary('Test deposit 2');
        $d2->setTitle('Tests More');
        $d2->setUuid('F89C2A08-96A8-478C-8944-92D8403E76C2');
        $d2->setDepositDate();
        $manager->persist($d2);
        $this->referenceRepository->setReference('deposit.2', $d2);        
        
        $manager->flush();
    }

    protected function getEnvironments() {
        return array('test', 'dev');
    }
}