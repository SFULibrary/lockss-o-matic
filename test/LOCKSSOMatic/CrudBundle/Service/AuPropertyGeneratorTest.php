<?php

namespace LOCKSSOMatic\CrudBundle\Service;

use Exception;
use LOCKSSOMatic\CoreBundle\Utilities\AbstractTestCase;
use LOCKSSOMatic\CrudBundle\Entity\Au;
use LOCKSSOMatic\CrudBundle\Entity\Content;

class AuPropertyGeneratorTest extends AbstractTestCase {
    
    /**
     *
     * @var AuPropertyGenerator
     */
    private $generator;
    
    public function setUp() {
        parent::setUp();
        $this->generator = $this->getContainer()->get('crud.propertygenerator');
    }
    
    public function fixtures()
    {
        return array(
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadAu',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadAuProperty',            
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadContentOwner',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadDeposit',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadContent',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadContentProperty',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadContentProvider',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPlugin',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPluginProperty',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPln',
        );
    }
    
    public function testSetUp() {
        $this->assertInstanceOf('LOCKSSOMatic\CrudBundle\Service\AuPropertyGenerator', $this->generator);
    }
    
    public function testGenerate() {
        $au = $this->references->getReference('au');
        $str = $this->generator->generateSymbol($au, 'au_name');
        $this->assertEquals('AU http://example.com 2007', $str);
    }
    
    public function testValidateContent() {
        $a1 = $this->em->find('LOCKSSOMaticCrudBundle:Au', 1);
        $this->generator->validateContent($a1);
        // no exception.
        $this->assertTrue(true);
    }
    
    public function testValidateContentEmptyAu() {
        $a1 = new Au();
        try {
            $this->generator->validateContent($a1);
        } catch(Exception $e) {
            $this->assertEquals('AU must have content to generate properties.', $e->getMessage());
            return;
        }
        $this->fail('no exception.');
    }   

    public function testValidateContentSingleItem() {
        $a1 = new Au();
        $a1->addContent(new Content());
        $this->generator->validateContent($a1);
        // no exception
        $this->assertTrue(true);
    }   
    
    public function testValidateContentBad() {
        $a1 = $this->em->find('LOCKSSOMaticCrudBundle:Au', 1);
        $a1->addContent(new Content());
        try {
            $this->generator->validateContent($a1);
        } catch(Exception $e) {
            $this->assertStringStartsWith('Content property mismatch', $e->getMessage());
            return;
        }
        $this->fail('no exception.');
    }

    public function testGenerateProperties() {
        $this->em->clear();
        $a1 = $this->em->find('LOCKSSOMaticCrudBundle:Au', 1);
        $this->generator->generateProperties($a1);
        $this->em->clear();
        $au = $this->em->find('LOCKSSOMaticCrudBundle:Au', 1);
        $this->assertCount(23, $au->getAuProperties());
        $this->assertEquals('pdq', $au->getAuPropertyValue('lockss_only'));
        # this is the wrong base_url - getAuPropertyValue() only finds the FIRST
        # property value, and we didn't clear the properties.
        $this->assertEquals('http://example.com', $au->getAuPropertyValue('base_url'));
    }    
    
    public function testGeneratePropertiesClear() {
        $this->em->clear();
        $a1 = $this->em->find('LOCKSSOMaticCrudBundle:Au', 1);
        $this->generator->generateProperties($a1, true);
        $this->em->clear();
        $au = $this->em->find('LOCKSSOMaticCrudBundle:Au', 1);
        $this->assertCount(16, $au->getAuProperties());
        $this->assertEquals('pdq', $au->getAuPropertyValue('lockss_only'));
        $this->assertEquals('http://example.com/path/htap', $au->getAuPropertyValue('base_url'));
    }    
}
