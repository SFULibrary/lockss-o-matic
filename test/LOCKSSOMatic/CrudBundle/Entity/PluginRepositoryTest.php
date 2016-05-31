<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace LOCKSSOMatic\CrudBundle\Entity;

use LOCKSSOMatic\CoreBundle\Utilities\AbstractTestCase;

/**
 * Description of PluginTest
 *
 * @author mjoyce
 */
class PluginRepositoryTest extends AbstractTestCase
{

    protected $repo;

    public function setUp()
    {
        parent::setUp();
        $this->repo = $this->em->getRepository('LOCKSSOMaticCrudBundle:Plugin');
    }

    public function fixtures()
    {
        return array(
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPlugin',
        );
    }
    
    public function testFindByPluginIdentifier() {
        $results = $this->repo->findByIdentifier('ca.sfu.plugin.test');
        $this->assertCount(1, $results);
    }
    
    public function testFindOneByPluginIdentifier() {
        $results = $this->repo->findOneByIdentifier('ca.sfu.plugin.test');
        $this->assertInstanceOf('LOCKSSOMatic\CrudBundle\Entity\Plugin', $results);
    }
    
    public function testPluginIds() {
        $results =$this->repo->pluginIds();
        $this->assertCount(1, $results);
        $this->assertEquals('ca.sfu.plugin.test', $results[0]['identifier']);
    }
    
    public function testFindVersions() {
        $results = $this->repo->findVersions('ca.sfu.plugin.test');
        $this->assertCount(1, $results);
        $this->assertInstanceOf('LOCKSSOMatic\CrudBundle\Entity\Plugin', $results[0]);
    }
}