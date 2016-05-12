<?php

namespace LOCKSSOMatic\CrudBundle\Service;

use LOCKSSOMatic\CoreBundle\Utilities\AbstractTestCase;
use LOCKSSOMatic\CrudBundle\Entity\Content;
use LOCKSSOMatic\CrudBundle\Entity\ContentProperty;

class AuIdGeneratorTest extends AbstractTestCase {
    
    /**
     *
     * @var AuIdGenerator
     */
    private $generator;
    
    public function setUp() {
        parent::setUp();
        $this->generator = $this->getContainer()->get('crud.au.idgenerator');
        $this->generator->setNondefinitionalCPDs(array(
            'ca.sfu.plugin.test' => array('lockss_only')
        ));
    }
    
    public function fixtures() {
        return array(
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadContentOwner',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadContentProvider',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadDeposit',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadAu',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadContent',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadContentProperty',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPlugin',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPluginProperty',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPln',
        );
    }
    
    public function testSetUp() {
        $this->assertInstanceOf('LOCKSSOMatic\CrudBundle\Service\AuIdGenerator', $this->generator);
    }
    
    public function testFromContent() {
        $content = $this->references->getReference('content.1');
        $this->assertEquals(
            'ca|sfu|plugin|test&base_url~http%3A%2F%2Fexample%2Ecom%2Fpath%2Fhtap', 
            $this->generator->fromContent($content, false)
        );
    }
    
    public function testFromContentLockss() {
        $content = $this->references->getReference('content.1');
        $this->assertEquals(
            'ca|sfu|plugin|test&base_url~http%3A%2F%2Fexample%2Ecom%2Fpath%2Fhtap&lockss_only~pdq', 
            $this->generator->fromContent($content, true)
        );
    }
}
