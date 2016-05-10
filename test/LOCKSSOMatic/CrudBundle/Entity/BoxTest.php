<?php

namespace LOCKSSOMatic\CrudBundle\Entity;

use LOCKSSOMatic\CoreBundle\Utilities\AbstractTestCase;

class BoxTest extends AbstractTestCase
{

    /**
     * @var Box
     */
    protected $box;

    public function setUp()
    {
        parent::setUp();
        $this->box = new Box();
    }
    
    public function testDefaults() {
        $this->assertInstanceOf('LOCKSSOMatic\CrudBundle\Entity\Box', $this->box);
        $this->assertCount(0, $this->box->getStatus());
        $this->assertEquals('TCP', $this->box->getProtocol());
        $this->assertEquals(9729, $this->box->getPort());
        $this->assertEquals(80, $this->box->getWebServicePort());
    }
    
    public function testResolveHostname() {
        $this->box->setHostname('localhost');
        $this->box->resolveHostname();
        $this->assertEquals('127.0.0.1', $this->box->getIpAddress());
    }

    public function testResolveHostnameForce() {
        $this->box->setHostname('localhost');
        $this->box->setIpAddress('8.8.8.8');
        $this->box->resolveHostname(true);
        $this->assertEquals('127.0.0.1', $this->box->getIpAddress());
    }

    public function testResolveHostnameInvalid() {
        $this->box->setHostname('far.away.from.here.jupiter');
        $this->box->resolveHostname();
        $this->assertEquals(null, $this->box->getIpAddress());
    }

}
