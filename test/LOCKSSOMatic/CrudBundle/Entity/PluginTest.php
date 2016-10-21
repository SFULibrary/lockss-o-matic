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
class PluginTest extends AbstractTestCase
{

    protected $plugin;

    public function setUp()
    {
        parent::setUp();
        $this->plugin = $this->em->find('LOCKSSOMaticCrudBundle:Plugin', 1);
    }

    public function fixtures()
    {
        return array(
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPlugin',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPluginProperty',
        );
    }
    
    public function testGetRootPluginProperties() {
        $props = $this->plugin->getRootPluginProperties();
        $this->assertCount(4, $props);
        $this->assertEquals('plugin_identifier', $props[0]->getPropertyKey());
        $this->assertEquals('plugin_config_props', $props[1]->getPropertyKey());
    }

    public function testPluginIdentifier() {
        $this->assertEquals('ca.sfu.plugin.test', $this->plugin->getPluginIdentifier());
    }

    public function testGetPluginConfigParams() {
        $props = $this->plugin->getPluginConfigParams();
        $this->assertCount(3, $props);
        $this->assertEquals('configparamdescr', $props[0]->getPropertyKey());
    }
    
    public function testGetPropertyValue() {
        $this->assertEquals('ca.sfu.plugin.test', $this->plugin->getPropertyValue('plugin_identifier'));
    }
    
    public function testGetProperty() {
        $prop = $this->plugin->getProperty('param.nop');
        $this->assertInstanceOf('LOCKSSOMatic\CrudBundle\Entity\PluginProperty', $prop);
        $this->assertEquals('param.nop', $prop->getPropertyKey());
        $this->assertEquals('not a real param', $prop->getPropertyValue());
    }
    
    public function testGetDefinitionalProperties() {
        $this->em->clear();
        $props = $this->plugin->getDefinitionalProperties();
        $this->assertCount(2, $props);
        $this->assertEquals('base_url', $props[0]);
    }
    
    public function testGetNonDefinitionalProperties() {
        $this->em->clear();
        $props = $this->plugin->getNonDefinitionalProperties();
        $this->assertCount(1, $props);
        $this->assertEquals('year', $props[0]);
    }
}
