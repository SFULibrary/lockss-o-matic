<?php

namespace LOCKSSOMatic\CrudBundle\Tests\Entity;

use LOCKSSOMatic\CoreBundle\Utilities\AbstractTestCase;
use LOCKSSOMatic\CrudBundle\Entity\Au;
use LOCKSSOMatic\CrudBundle\Entity\Content;

class AuTest extends AbstractTestCase
{

    /**
     * @var Au
     */
    protected $au;

    protected function setUp()
    {
        parent::setUp();
        $this->au = $this->references->getReference('au');
    }

    public function testEmptyGetSize()
    {
        $this->assertEquals(0, $this->au->getContentSize());
    }

    public function testContentGetSize()
    {
        $c1 = new Content();
        $c1->setSize(120);
        $this->au->addContent($c1);
        $this->assertEquals(120, $this->au->getContentSize());

        $c2 = new Content();
        $c2->setSize(200);
        $this->au->addContent($c2);
        $this->assertEquals(320, $this->au->getContentSize());
    }

    public function testNullsGetSize()
    {
        $c1 = new Content();
        $c1->setSize(120);
        $this->au->addContent($c1);

        $c2 = new Content();
        // No setSize().
        $this->au->addContent($c2);
        $this->assertEquals(120, $this->au->getContentSize());

        $c3 = new Content();
        $c3->setSize(200);
        $this->au->addContent($c3);
        $this->assertEquals(320, $this->au->getContentSize());
    }

    public function testGetRootPluginProperties()
    {
        $props = $this->au->getRootPluginProperties();
        $this->assertEquals(1, count($props));
        $this->assertEquals('TestStuffGoesHere', $props[0]->getPropertyKey());
    }

    public function testGetAuProperty()
    {
        $this->assertEquals('http://example.com', $this->au->getAuProperty('base_url'));
    }

    public function testGetAuPropertyEncoded()
    {
        $this->assertEquals('http%3A%2F%2Fexample%2Ecom', $this->au->getAuProperty('base_url', true));
    }

    public function testGetDefinitionalProperties()
    {
        $props = $this->au->getDefinitionalProperties();
        $this->assertEquals(1, count($props));
    }

    public function testGenerateAuid()
    {
        $this->assertEquals('ca|sfu|test&base_url~http%3A%2F%2Fexample%2Ecom', $this->au->getAuid());
    }

}
