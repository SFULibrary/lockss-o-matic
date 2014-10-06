<?php

namespace LOCKSSOMatic\CRUDBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CRUDBundle\Entity\Content;

class LoadContentData extends AbstractFixture implements OrderedFixtureInterface {
    
    public function getOrder()
    {
        return 4; // after deposits and aus
    }

    public function load(ObjectManager $manager)
    {
        $object = new Content();
        $object->setUrl('http://example.com/path/item/1');
        $object->setTitle('Some example title');
        $object->setSize('41445');
        $object->setChecksumType('md5');
        $object->setChecksumValue('eb4585ad9fe0426781ed7c49252f8225');
        $object->setRecrawl(1);
        $object->setDeposit($this->getReference('deposit-1'));
        $object->setAu($this->getReference('au-1'));
        
        $manager->persist($object);
        $manager->flush();
        $this->setReference('cp-1', $object);
    }

}