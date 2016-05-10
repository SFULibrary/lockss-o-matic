<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

use LOCKSSOMatic\CoreBundle\Utilities\AbstractTestCase;

class ContentTest extends AbstractTestCase
{

    /**
     * @var Content
     */
    protected $content;

    public function setUp()
    {
        parent::setUp();
        $this->content = $this->em->find('LOCKSSOMaticCrudBundle:Content', 1);
    }
    
    public function fixtures() {
        return array(
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadDeposit',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadContent',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadContentProperty',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadAu',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadContentOwner',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadContentProvider',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPlugin',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPln',
        );
    }
    
    public function testDefaults() {
        $content = new Content();
        $this->assertCount(0, $content->getContentProperties());
    }
    
    public function testSetSize() {
        $content = new Content();
        $content->setSize(123);
        $content->setSize('');
        $this->assertEquals(123, $content->getSize());
    }

    public function testGetContentPropertyValueEmpty() {
        $content = new Content();
        $this->assertEquals(null, $content->getContentPropertyValue('fooo'));
    }
    
    public function testGetContentPropertyValueEmptyEncoded() {
        $content = new Content();
        $this->assertEquals(null, $content->getContentPropertyValue('fooo', true));
    }
    
    public function testSetPropertyValue() {
        $property = new ContentProperty();
        $property->setPropertyValue('abcd');
        $this->assertFalse($property->isList());
        $this->assertEquals('abcd', $property->getPropertyValue());
    }
    
    public function testSetPropertyValueList() {
        $property = new ContentProperty();
        $property->setPropertyValue(array('a', 'b'));
        $this->assertTrue($property->isList());
        $this->assertEquals(array('a', 'b'), $property->getPropertyValue());
    }
    
    public function testGetContentPropertyValue() {
        $this->assertEquals('http://example.com', $this->content->getContentPropertyValue('prop1'));
    }
    
    public function testGetContentPropertyValueEncoded() {
        $this->assertEquals('http%3A%2F%2Fexample%2Ecom', $this->content->getContentPropertyValue('prop1', true));
    }
    
    public function testGetContentPropertyValueList() {
        $this->assertEquals(array('abc/', 'pdq'), $this->content->getContentPropertyValue('prop2'));
    }
    
    public function testGetContentPropertyValueListEncoded() {
        $this->assertEquals(array('abc%2F', 'pdq'), $this->content->getContentPropertyValue('prop2', true));
    }
}
