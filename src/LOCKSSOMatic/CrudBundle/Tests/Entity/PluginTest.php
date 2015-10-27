<?php

namespace LOCKSSOMatic\CrudBundle\Tests\Entity;

use LOCKSSOMatic\CoreBundle\Utilities\AbstractTestCase;
use LOCKSSOMatic\CrudBundle\Entity\Plugin;
use LOCKSSOMatic\CrudBundle\Entity\PluginProperty;

class PluginTest extends AbstractTestCase
{

    /**
     * @var Plugin
     */
    protected $plugin;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->plugin = $this->references->getReference('plugin');
    }

    public function testGetRootPluginProperties() {
        /** @var PluginProperty[] $props */
        $props = $this->plugin->getRootPluginProperties();
        $this->assertEquals(6, count($props));
        $this->assertEquals($props[0]->getPropertyKey(), 'plugin_name');
        $this->assertEquals($props[1]->getPropertyKey(), 'plugin_version');
        $this->assertEquals($props[2]->getPropertyKey(), 'plugin_identifier');
        $this->assertEquals($props[3]->getPropertyKey(), 'au_start_url');
        $this->assertEquals($props[4]->getPropertyKey(), 'au_name');
        $this->assertEquals($props[5]->getPropertyKey(), 'plugin_config_props');
    }

    public function testGetPluginIdentifier() {
        $p = $this->plugin->getPluginIdentifier();
        $this->assertEquals('ca.sfu.test', $p);
    }

    public function testGetPluginConfigParams() {
        $props = $this->plugin->getPluginConfigParams();
        $this->assertEquals(2, count($props));
        $this->assertEquals($props[0]->getPropertyKey(), 'configparamdescr');
        $this->assertNull($props[0]->getPropertyValue());
        $this->assertEquals($props[1]->getPropertyKey(), 'configparamdescr');
        $this->assertNull($props[1]->getPropertyValue());
    }

    public function testGetDefinitionalProperties() {
        $props = $this->plugin->getDefinitionalProperties();
        $this->assertEquals(1, count($props));
        $this->assertEquals($props[0], 'base_url');
    }

}
