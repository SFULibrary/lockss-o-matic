<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

use LOCKSSOMatic\CoreBundle\Utilities\AbstractTestCase;

class BoxStatusTest extends AbstractTestCase
{
    /**
     * @var Status
     */
    protected $boxStatus;

    public function setUp()
    {
        parent::setUp();
        $this->boxStatus = $this->em->find('LOCKSSOMaticCrudBundle:BoxStatus', 1);
    }

    public function fixtures()
    {
        return array(
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadBox',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPln',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadBoxStatus',
        );
    }
    
    public function testDefaults() {
    }
    
    public function testBoxGetCurrentStatus() {
        $status = $this->em->find('LOCKSSOMaticCrudBundle:Box', 1)->getCurrentStatus();
        $this->assertEquals(2, $status->getId());
    }
    
    public function testGetActiveCount() {
        $this->assertEquals(23, $this->boxStatus->getActiveCount());
    }
    
    public function testGetFree() {
        $this->assertEquals(123, $this->boxStatus->getFree());
    }
    
    public function testGetSize() {
        $this->assertEquals(432, $this->boxStatus->getSize());
    }
    
    public function testGetUsed() {
        $this->assertEquals(309, $this->boxStatus->getUsed());
    }
    
}
