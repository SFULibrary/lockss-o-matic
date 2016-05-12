<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace LOCKSSOMatic\CrudBundle\Entity;

/**
 * Description of ContentProviderTest
 *
 * @author mjoyce
 */
class ContentProviderTest extends \PHPUnit_Framework_TestCase 
{
    protected $provider;
    
    public function setUp() {
        parent::setUp();
        $this->provider = new ContentProvider();
    }
    
    public function testGetPermissionHost() {
        $this->provider->setPermissionurl('http://t0.example.com/path/to/permissions');
        $this->assertEquals('t0.example.com', $this->provider->getPermissionHost());
    }
    
    public function testGetContent() {
        $this->assertCount(0, $this->provider->getContent());
    }
}

