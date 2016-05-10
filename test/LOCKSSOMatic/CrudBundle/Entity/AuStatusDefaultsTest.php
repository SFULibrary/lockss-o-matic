<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

use LOCKSSOMatic\CoreBundle\Utilities\AbstractTestCase;

class AuStatusDefaultsTest extends AbstractTestCase {

    protected $auStatus;
    
    public function setUp() {
        parent::setUp();
        $this->auStatus = new AuStatus();
    }

    public function testGetStatus() {
        $this->assertNull($this->auStatus->getStatus());
    }
    
    public function testGetAu() {
        $this->assertNull($this->auStatus->getAu());
    }
    
    public function testGetPln() {
        $this->assertNull($this->auStatus->getPln());
    }
    
    public function testGetBox() {
        $this->assertNull($this->auStatus->getBox());
    }
    
    public function testContentSize() {
        $this->assertNull($this->auStatus->getContentSize());
    }

    public function testDiskUsage() {
        $this->assertNull($this->auStatus->getDiskUsage());
    }
    
    public function testGetLastCrawl() {
        $this->assertEquals(0, $this->auStatus->getLastCrawl());
    }

    public function testGetLastCrawlResult() {
        $this->assertEquals('Never crawled', $this->auStatus->getLastCrawlResult());
    }
    
    public function testGetLastPoll() {
        $this->assertEquals(0, $this->auStatus->getLastPoll());
    }

    public function testGetLastPollResult() {
        $this->assertEquals('Never polled', $this->auStatus->getLastPollResult());
    }
    
    public function testGetAuStatus() {
        $this->assertNull($this->auStatus->getAuStatus());
    }

}
