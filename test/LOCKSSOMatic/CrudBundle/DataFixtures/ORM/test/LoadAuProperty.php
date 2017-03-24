<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test;

use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CoreBundle\Utilities\AbstractDataFixture;
use LOCKSSOMatic\CrudBundle\Entity\Au;
use LOCKSSOMatic\CrudBundle\Entity\AuProperty;

/** 
 * Description of LoadAu
 *
 * @author mjoyce
 */
class LoadAuProperty extends AbstractDataFixture
{
    public function getOrder() {
        return 4; 
    }
    
    private function buildProp(ObjectManager $manager, $id, $propName, $propValue, AuProperty $grandparent) {
        $parent = new AuProperty();
        $parent->setAu($grandparent->getAu());
        $parent->setParent($grandparent);
        $parent->setPropertyKey($id);
        $manager->persist($parent);
        
        $key = new AuProperty();
        $key->setAu($parent->getAu());
        $key->setParent($parent);
        $key->setPropertyKey('key');
        $key->setPropertyValue($propName);
        $manager->persist($key);
        
        $value = new AuProperty();
        $value->setAu($parent->GetAu());
        $value->setParent($parent);
        $value->setPropertyKey('value');
        $value->setPropertyValue($propValue);
        $manager->persist($value);
        
        return $parent;
    }
    
    protected function doLoad(ObjectManager $manager)
    {
        $au = $this->referenceRepository->getReference('au');
       
        $root = new AuProperty();
        $root->setAu($au);
        $root->setPropertyKey('root');
        $manager->persist($root);
        $this->referenceRepository->addReference('au.prop.root', $root);
        
        $c1 = $this->buildProp($manager, 'param.1', 'base_url', 'http://example.com', $root);
        $this->referenceRepository->addReference('au.prop.c1', $c1);
        
        $c2 = $this->buildProp($manager, 'param.2', 'year', '2007', $root);
        $this->referenceRepository->addReference('au.prop.c2', $c2);
        
        $manager->flush();        
    }

    protected function getEnvironments()
    {
        return array('test', 'dev');
    }

//put your code here
}
