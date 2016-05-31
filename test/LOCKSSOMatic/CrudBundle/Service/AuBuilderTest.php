<?php

namespace LOCKSSOMatic\CrudBundle\Service;

use LOCKSSOMatic\CoreBundle\Utilities\AbstractTestCase;
use LOCKSSOMatic\CrudBundle\Entity\Content;

class AuBuilderTest extends AbstractTestCase {
    
    /**
     *
     * @var AuBuilder
     */
    protected $builder;
    
    public function setUp() {
        parent::setUp();
        $this->builder = $this->getContainer()->get('crud.builder.au');
        $idgenerator = $this->getMockBuilder('LOCKSSOMatic\CrudBundle\Service\AuIdGenerator')
            ->disableOriginalConstructor()            
            ->getMock();
        $idgenerator->method('fromContent')
            ->willReturn('foo:bar');
        $this->builder->setAuIdGenerator($idgenerator);
    }
    
    public function fixtures() {
        return array(
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadContentOwner',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadContentProvider',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadDeposit',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPlugin',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPln',
        );
    }
    
    public function testSetUp() {
        $this->assertInstanceOf('LOCKSSOMatic\CrudBundle\Service\AuBuilder', $this->builder);
    }
    
    public function testFromContent() {
        $au = $this->em->find('LOCKSSOMaticCrudBundle:Au', 1);
        $this->assertNull($au);
        $content = $this->contentFixture();
        $this->builder->fromContent($content);
        $builtAu = $this->em->find('LOCKSSOMaticCrudBundle:Au', 1);
        $this->assertNotNull($builtAu);
        
        $this->assertEquals('foo:bar', $builtAu->getAuId()); // from the mock.
        $this->assertEquals(1, $builtAu->getPln()->getId());
        $this->assertEquals(1, $builtAu->getContentProvider()->getId());
        $this->assertEquals(1, $builtAu->getPlugin()->getId());
    }
    
    private function contentFixture() {
        // must build a content item here - one that's already in an AU
        // won't do.
        $content = new Content();
        $content->setUrl('http://example.com/path/file');
        $content->setTitle('Titles are good');
        $content->setDepositDate();
        $content->setRecrawl(true);
        $content->setDeposit($this->references->getReference('deposit.1'));
        $this->em->persist($content);
        
        return $content;
    }
}
