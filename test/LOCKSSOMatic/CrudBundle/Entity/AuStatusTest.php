<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

use LOCKSSOMatic\CoreBundle\Utilities\AbstractTestCase;

class AuStatusTest extends AbstractTestCase {

    protected $auStatus;
    
    public function setUp() {
        parent::setUp();
        $this->auStatus = $this->em->getRepository('LOCKSSOMaticCrudBundle:AuStatus')
            ->find(1);
    }

    public function fixtures() {
        return array(
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadAu',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadContentOwner',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadContentProvider',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPlugin',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPln',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadBox',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadAuStatus',
        );
    }

    public function testAuGetAuStatus() {
        $this->em->clear();
        $au = $this->em->getRepository('LOCKSSOMaticCrudBundle:Au')->find(1);
        $this->assertCount(2, $au->getAuStatus());
    }

    public function testAuStatus() {
        $this->em->clear();
        $au = $this->em->getRepository('LOCKSSOMaticCrudBundle:Au')->find(1);
        $this->assertCount(2, $au->getAuStatus());
        $this->assertEquals('ok', $au->status());
    }

    public function testGetQueryDate() {
        $this->assertInstanceOf('DateTime', $this->auStatus->getQueryDate());
    }

    public function testGetStatus() {
        $expected = array(
            'status' => 'ok',
            'contentSize' => 123,
            'diskUsage' => 432,
            'lastPoll' => '2012-10-10',
            'lastCrawlResult' => 'success',
            'lastPollResult' => 'agreement',
        );
        $this->assertEquals($expected, $this->auStatus->getStatus());
    }
    
    public function testGetPln() {
        $this->assertEquals(1, $this->auStatus->getPln()->getId());
    }
    
    public function testGetBox() {
        $this->assertEquals(1, $this->auStatus->getBox()->getId());
    }
    
    public function testContentSize() {
        $this->assertEquals(123, $this->auStatus->getContentSize());
    }

    public function testDiskUsage() {
        $this->assertEquals(432, $this->auStatus->getDiskUsage());
    }
    
    public function testGetLastCrawl() {
        $this->assertEquals('2012-10-10', $this->auStatus->getLastCrawl());
    }

    public function testGetLastCrawlResult() {
        $this->assertEquals('success', $this->auStatus->getLastCrawlResult());
    }
    
    public function testGetLastPoll() {
        $this->assertEquals('2012-10-10', $this->auStatus->getLastPoll());
    }

    public function testGetLastPollResult() {
        $this->assertEquals('agreement', $this->auStatus->getLastPollResult());
    }
    
    public function testGetAuStatus() {
        $this->assertEquals('ok', $this->auStatus->getAuStatus());
    }
}
