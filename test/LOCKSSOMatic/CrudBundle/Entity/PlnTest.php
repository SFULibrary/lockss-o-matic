<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace LOCKSSOMatic\CrudBundle\Entity;

use PHPUnit_Framework_TestCase;

/**
 * Description of PlnTest
 *
 * @author mjoyce
 */
class PlnTest extends PHPUnit_Framework_TestCase 
{
    protected $pln;
    
    public function setUp() {
        parent::setUp();
        $this->pln = new Pln();
    }
    
    public function testGetPropertySingle() {
        $this->pln->setProperty('foo', 'bar');
        $this->assertEquals('bar', $this->pln->getProperty('foo'));
    }
    
    public function testGetPropertyArray() {
        $this->pln->setProperty('foo', array('a', 'b'));
        $this->assertEquals(array('a', 'b'), $this->pln->getProperty('foo'));
    }
    
    public function testGetPropertyForceArray() {
        $this->pln->setProperty('foo', 'ab');
        $this->assertEquals(array('ab'), $this->pln->getProperty('foo', true));
    }
    
    public function testGetPropertyArrayRequired() {
        $this->pln->setProperty('org.lockss.titleDbs', 'ab');
        $this->assertEquals(array('ab'), $this->pln->getProperty('org.lockss.titleDbs', true));
    }
    
    public function testGetPropertyNull() {
        $this->assertNull($this->pln->getProperty('foooo'));
    }
}
