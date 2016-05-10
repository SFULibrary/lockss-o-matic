<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

use LOCKSSOMatic\CoreBundle\Utilities\AbstractTestCase;

class AuTest extends AbstractTestCase
{

    /**
     * @var Au
     */
    protected $au;

    public function setUp()
    {
        parent::setUp();
        $this->au = new Au();
    }

    public function fixtures()
    {
        return array(
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadAu',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadAuProperty',            
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadContentOwner',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadContentProvider',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPlugin',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPln',
        );
    }

    public function testDefaults()
    {
        $this->assertInstanceOf('LOCKSSOMatic\CrudBundle\Entity\Au', $this->au);
        $this->assertCount(0, $this->au->getAuProperties());
        $this->assertCount(0, $this->au->getAuStatus());
        $this->assertCount(0, $this->au->getContent());
    }

    public function testGetRootPluginProperties()
    {
        $props = $this->references->getReference('au')->getRootPluginProperties();
        $this->assertCount(1, $props);
        $this->assertEquals('root', $props[0]->getPropertyKey());
    }

    public function testGetAuPropertyValue()
    {
        $au = $this->references->getReference('au');
        $this->assertEquals('2007', $au->getAuPropertyValue('year'));   
    }

    public function testGetAuPropertyValueEncoded()
    {
        $au = $this->references->getReference('au');
        $this->assertEquals('http%3A%2F%2Fexample%2Ecom', $au->getAuPropertyValue('base_url', true));
    }

    public function testGetAuPropertyValueNull()
    {
        $au = $this->references->getReference('au');
        $this->assertEquals('', $au->getAuPropertyValue('cheeses', true));
    }

    public function testGetAuProperty()
    {
        $au = $this->references->getReference('au');
        $prop = $au->getAuProperty('year');
        $this->assertEquals('param.2', $prop->getPropertyKey());
    }

    public function testGetAuPropertyNull()
    {
        $au = $this->references->getReference('au');
        $prop = $au->getAuProperty('cheeses');
        $this->assertEquals(null, $prop);
    }    
    
    public function testGetContentSizeNull() {
        // see AuContentTest for tests with content - they require more data 
        // fixutres.
        $this->assertEquals(0, $this->au->getContentSize());
    }
    
    public function testStatusNull() {
        $this->assertNull($this->au->status());
    }
}
