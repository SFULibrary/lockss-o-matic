<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

use LOCKSSOMatic\CoreBundle\Utilities\AbstractTestCase;

class AuContentTest extends AbstractTestCase {
    
    /**
     * @var Au
     */
    protected $au;
    
    public function setUp() {
        parent::setUp();
        $this->au = $this->references->getReference('au');
    }
    
    public function fixtures() {
        return array(
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadDeposit',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadContent',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadAu',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadContentOwner',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadContentProvider',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPlugin',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPln',
        );
    }
    
    public function testCount() {
        $this->assertCount(3, $this->au->getContent());
    }    
    
    public function testContentSize() {
        $this->assertEquals(679, $this->au->getContentSize());
    }
}
