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
class PluginPropertyTest extends AbstractTestCase
{

    private $pluginProperty;

    public function setUp()
    {
        parent::setUp();
        $this->pluginProperty = new PluginProperty();
    }
    
    public function testGetPropertyValueSingle() {
        $this->pluginProperty->setPropertyValue('bar');
        $this->assertEquals('bar', $this->pluginProperty->getPropertyValue());
    }
    
    public function testGetPropertyValueArray() {
        $this->pluginProperty->setPropertyValue(array('a', 'b'));
        $this->assertEquals(array('a', 'b'), $this->pluginProperty->getPropertyValue());
    }
    
    public function testGetPropertyValueNull() {
        $this->assertNull($this->pluginProperty->getPropertyValue('foooo'));
    }
}
