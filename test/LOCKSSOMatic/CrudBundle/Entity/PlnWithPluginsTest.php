<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace LOCKSSOMatic\CrudBundle\Entity;

use LOCKSSOMatic\CoreBundle\Utilities\AbstractTestCase;

/**
 * Description of PlnTest
 *
 * @author mjoyce
 */
class PlnWithPluginsTest extends AbstractTestCase
{

    protected $pln;

    public function setUp()
    {
        parent::setUp();
        $this->pln = $this->em->find('LOCKSSOMaticCrudBundle:Pln', 1);
    }

    public function fixtures()
    {
        return array(
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPlugin',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPln',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadContentOwner',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadContentProvider',
        );
    }
    
    public function testGetPlugins() {
        $this->em->clear();
        $plugins = $this->pln->getPlugins();
        $this->assertCount(1, $plugins);
        $this->assertArrayHasKey('ca.sfu.plugin.test', $plugins);
        $this->assertInstanceOf('LOCKSSOMatic\CrudBundle\Entity\Plugin', $plugins['ca.sfu.plugin.test']);
    }


}
