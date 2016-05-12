<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test;

use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CoreBundle\Utilities\AbstractDataFixture;
use LOCKSSOMatic\CrudBundle\Entity\Content;

/**
 * Description of LoadAu
 *
 * @author mjoyce
 */
class LoadContent extends AbstractDataFixture
{
    public function getOrder() {
        return 4; // After Deposit.
    }
    
    protected function doLoad(ObjectManager $manager)
    {
        $c1 = new Content();
        $c1->setAu($this->referenceRepository->getReference('au'));
        $c1->setChecksumType('SHA1');
        $c1->setChecksumValue('abc123');
        $c1->setDeposit($this->referenceRepository->getReference('deposit.1'));
        $c1->setSize(123);
        $c1->setTitle("Issue 1");
        $c1->setUrl("http://example.com/path/file-1");
        $c1->setDepositDate();
        $c1->setRecrawl(false);
        $manager->persist($c1);
        $this->referenceRepository->addReference('content.1', $c1);        

        $c2 = new Content();
        $c2->setAu($this->referenceRepository->getReference('au'));
        $c2->setChecksumType('SHA1');
        $c2->setChecksumValue('abc223');
        $c2->setDeposit($this->referenceRepository->getReference('deposit.1'));
        $c2->setSize(223);
        $c2->setTitle("Issue 2");
        $c2->setUrl("http://example.com/path/file-2");
        $c2->setDepositDate();
        $c2->setRecrawl(false);
        $manager->persist($c2);
        $this->referenceRepository->addReference('content.2', $c2);        

        $c3 = new Content();
        $c3->setAu($this->referenceRepository->getReference('au'));
        $c3->setChecksumType('SHA1');
        $c3->setChecksumValue('abc333');
        $c3->setDeposit($this->referenceRepository->getReference('deposit.2'));
        $c3->setSize(333);
        $c3->setTitle("Issue 3");
        $c3->setUrl("http://example.com/path/file-3");
        $c3->setDepositDate();
        $c3->setRecrawl(false);
        $manager->persist($c3);
        $this->referenceRepository->addReference('content.3', $c3);        

        $manager->flush();        
    }

    protected function getEnvironments()
    {
        return array('test');
    }

//put your code here
}
