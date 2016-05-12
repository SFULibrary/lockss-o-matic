<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace LOCKSSOMatic\CrudBundle\Entity;

use LOCKSSOMatic\CoreBundle\Utilities\AbstractTestCase;

/**
 * Description of ContentProviderTest
 *
 * @author mjoyce
 */
class ContentProviderWithContentTest extends AbstractTestCase
{
    protected $provider;
    
    public function setUp() {
        parent::setUp();
        $this->provider = $this->em->find('LOCKSSOMaticCrudBundle:ContentProvider', 1);
    }
    
    public function fixtures() {
        return array(
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadContent',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadDeposit',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadContentOwner',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadContentProvider',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadAu',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPlugin',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPln',
        );
    }
    
    public function testGetContent() {
        $content = $this->provider->getContent();
        $this->assertCount(3, $content);
    }
    
}

