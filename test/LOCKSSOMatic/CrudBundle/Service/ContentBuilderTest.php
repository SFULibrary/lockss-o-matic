<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace LOCKSSOMatic\CrudBundle\Service;

use LOCKSSOMatic\CoreBundle\Utilities\AbstractTestCase;
use LOCKSSOMatic\CrudBundle\Entity\Content;
use LOCKSSOMatic\SwordBundle\Utilities\Namespaces;
use SimpleXMLElement;

/**
 * Description of ContentBuilderTest
 *
 * @author michael
 */
class ContentBuilderTest extends AbstractTestCase {
    
    /**
     * @var ContentBuilder
     */
    protected $builder;
    
    public function setUp() {
        parent::setUp();
        $this->builder = $this->getContainer()->get('crud.builder.content');
    }
    
    public function testBuildProperty() {
        $content = new Content();
        $prop = $this->builder->buildProperty($content, 'foo', 'kapow');
        $this->assertEquals($content, $prop->getContent());
        $this->assertEquals('foo', $prop->getPropertyKey());
        $this->assertEquals('kapow', $prop->getPropertyValue());
    }
    
    public function testFromSimpleXml() {
        $xml = $this->getXml();
        $content = $this->builder->fromSimpleXML($xml);
        $this->assertInstanceOf('LOCKSSOMatic\CrudBundle\Entity\Content', $content);
        $this->assertEquals(1234, $content->getSize());
        $this->assertEquals('SHA1', $content->getChecksumType());
        $this->assertEquals('abc123123', $content->getChecksumValue());
        $this->assertEquals('http://example.com/path/to/deposit.zip', $content->getUrl());
        $this->assertTrue($content->getRecrawl());
        $this->assertEquals('Hello World', $content->getTitle());
        $this->assertEquals('Hello World', $content->getContentPropertyValue('journalTitle'));
        $this->assertEquals('PLN Publisher', $content->getContentPropertyValue('publisher'));
        $this->assertEquals('http://example.com', $content->getContentPropertyValue('base_url'));
        $this->assertEquals('http://example.com/permission', $content->getContentPropertyValue('permission_url'));
        $this->assertEquals(3, $content->getContentPropertyValue('container_number'));
        $this->assertEquals('http://example.com', $content->getContentPropertyValue('base_url'));
    }
      
    public function testFromArray() {
        $data = $this->getArray();
        $content = $this->builder->fromArray($data);
        $this->assertInstanceOf('LOCKSSOMatic\CrudBundle\Entity\Content', $content);
        $this->assertEquals(1234, $content->getSize());
        $this->assertEquals('SHA1', $content->getChecksumType());
        $this->assertEquals('http://example.com/path/to/deposit.zip', $content->getUrl());
        $this->assertTrue($content->getRecrawl());
        $this->assertEquals('Hello World', $content->getTitle());
        $this->assertEquals('Hello World', $content->getContentPropertyValue('journalTitle'));
        $this->assertEquals('PLN Publisher', $content->getContentPropertyValue('publisher'));
        $this->assertEquals('http://example.com', $content->getContentPropertyValue('base_url'));
        $this->assertEquals('http://example.com/permission', $content->getContentPropertyValue('permission_url'));
        $this->assertEquals(3, $content->getContentPropertyValue('container_number'));
        $this->assertEquals('http://example.com', $content->getContentPropertyValue('base_url'));
    }

    private function getArray() {
        return array(
            'size' => 1234,
            'checksum type' => 'SHA1',
            'checksum value' => 'abc123123',
            'url' => 'http://example.com/path/to/deposit.zip',
            'title' => 'Hello World',
            'journalTitle' => 'Hello World',
            'base_url' => 'http://example.com',
            'publisher' => 'PLN Publisher',
            'permission_url' => 'http://example.com/permission',
            'container_number' => 3,
        );
    }
    
    private function getXml() {
        $str = <<<XML
        <content xmlns="http://lockssomatic.info/SWORD2"
                 xmlns:pkp="http://pkp.sfu.ca/SWORD"
            size="1234"
            checksumType="SHA1"
            checksumValue="abc123123"
            pkp:volume="2"
            pkp:issue="1"
            pkp:pubDate="2015-01-01"
            pkp:journalTitle="Hello World"
            pkp:journalUrl="http://hi.example.com"
            pkp:issn="1234-1234"
            pkp:publisher="PLN Publisher"
            pkp:publisherName="World Publ. Inc."
            pkp:publisherUrl="http://wp.example.com">
                http://example.com/path/to/deposit.zip
                <property name="base_url" value="http://example.com"/>
                <property name="permission_url" value="http://example.com/permission"/>
                <property name="container_number" value="3"/>
        </content>
XML;
        $xml = new SimpleXMLElement($str);
        $ns = new Namespaces();
        $ns->registerNamespaces($xml);
        return $xml;
    }
}
