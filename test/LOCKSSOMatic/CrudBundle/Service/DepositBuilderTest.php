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
class DepositBuilderTest extends AbstractTestCase {
    
    /**
     * @var DepositBuilder
     */
    protected $builder;
    
    public function setUp() {
        parent::setUp();
        $this->builder = $this->getContainer()->get('crud.builder.deposit');
    }
    
    public function fixtures() {
        return array(
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadContentProvider',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadContentOwner',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPln',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPlugin',
        );
    }
    
    public function testFromSimpleXml() {
        $xml = $this->getXml();
        $deposit = $this->builder->fromSimpleXML($xml);
        $this->assertInstanceOf('LOCKSSOMatic\CrudBundle\Entity\Deposit', $deposit);
        $this->assertEquals('771E96EC-5486-4E34-A1F6-AB113AFB642D', $deposit->getUuid());        
        $this->assertEquals('Test Deposit', $deposit->getTitle());
        $this->assertEquals('', $deposit->getSummary());
    }
    
    public function testFromForm() {
        $stub = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->getMock();
        $stub->method('getData')
            ->willReturn(array(
                'title' => 'Test Deposit',
                'summary' => 'Summary judgement',
                'uuid' => '771E96EC-5486-4E34-A1F6-AB113AFB642D'
            ));
        
        $deposit = $this->builder->fromForm($stub, $this->references->getReference('contentprovider'));
        $this->assertInstanceOf('LOCKSSOMatic\CrudBundle\Entity\Deposit', $deposit);
        $this->assertEquals('771E96EC-5486-4E34-A1F6-AB113AFB642D', $deposit->getUuid());        
        $this->assertEquals('Test Deposit', $deposit->getTitle());
        $this->assertEquals('Summary judgement', $deposit->getSummary());        
    }
          
    public function testFromFormNullUUid() {
        $stub = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->getMock();
        $stub->method('getData')
            ->willReturn(array(
                'title' => 'Test Deposit',
                'summary' => 'Summary judgement',
                'uuid' => null,
            ));
        
        $deposit = $this->builder->fromForm($stub, $this->references->getReference('contentprovider'));
        $this->assertInstanceOf('LOCKSSOMatic\CrudBundle\Entity\Deposit', $deposit);
        $this->assertEquals('Test Deposit', $deposit->getTitle());
        $this->assertEquals('Summary judgement', $deposit->getSummary());        
        $this->assertEquals(36, strlen($deposit->getUuid()));        
    }    

    public function testFromFormNoUUid() {
        $stub = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->getMock();
        $stub->method('getData')
            ->willReturn(array(
                'title' => 'Test Deposit',
                'summary' => 'Summary judgement',
            ));
        
        $deposit = $this->builder->fromForm($stub, $this->references->getReference('contentprovider'));
        $this->assertInstanceOf('LOCKSSOMatic\CrudBundle\Entity\Deposit', $deposit);
        $this->assertEquals('Test Deposit', $deposit->getTitle());
        $this->assertEquals('Summary judgement', $deposit->getSummary());        
        $this->assertEquals(36, strlen($deposit->getUuid()));        
    }    

    private function getXml() {
        $str = <<<XML
<entry xmlns="http://www.w3.org/2005/Atom"
        xmlns:dcterms="http://purl.org/dc/terms/"
        xmlns:lom="http://lockssomatic.info/SWORD2"
        xmlns:pkp="http://pkp.sfu.ca/SWORD">
    <title>Test Deposit</title>
    <id>urn:uuid:771E96EC-5486-4E34-A1F6-AB113AFB642D</id>
    <updated>2016-01-01</updated>
    <author><name>J Testing</name></author>
    <summary type="text">
        Content deposited to LOCKSS-O-Matic via the PKP PLN Staging Server.
    </summary>
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
</entry>
XML;
        $xml = new SimpleXMLElement($str);
        $ns = new Namespaces();
        $ns->registerNamespaces($xml);
        return $xml;
    }
}
