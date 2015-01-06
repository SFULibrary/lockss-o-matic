<?php
/* 
 * The MIT License
 *
 * Copyright 2014. Michael Joyce <ubermichael@gmail.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace LOCKSSOMatic\PluginBundle\Tests\Plugins\dummy;

use Doctrine\ORM\EntityManager;
use LOCKSSOMatic\CRUDBundle\Entity\Plugins;
use LOCKSSOMatic\PluginBundle\Tests\Plugins\dummy\PluginTester;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;

/**
 * Test the plugin data methods of AbstractPluginTest.
 */
class AbstractPluginTest extends KernelTestCase
{

    /** @var Container */
    private $container;

    /** @var EntityManager */
    private $em;

    /**
     * This test requires a kernel, entity manager, and a container so create them.
     * The container is the important part, as it provides the plugin service.
     */
    public function __construct()
    {
        parent::__construct();
        static::bootKernel();
        $this->container = static::$kernel->getContainer();
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
    }
    
    public function testSettings() {
        $pt = new PluginTester();
        $this->assertEquals(array('foo' => 'bar', 'baz' => 'quux'), $pt->getSetting('setting1'));
        $this->assertEquals(true, $pt->getSetting('setting2'));
        $this->assertEquals(array('yes', 'again', 'nothankyou'), $pt->getSetting('setting3'));
    }

    public function testSettingsOverride() {
        $pt = new PluginTester();
        $pt->loadSettings(__DIR__ . '/overridden.yml');

        $this->assertTrue($pt->getSetting('set'));
        $this->assertEquals(array('fluffy', 'scruffy', 'poofy'), $pt->getSetting('dogs'));
        $this->assertEquals(array('friendly' => 'yes', 'pettable' => 'no', 'alergens' => 'quite possibly'), $pt->getSetting('cats'));
    }

    /**
     * Test setting some plugin data.
     */
    public function testSetPluginData()
    {
        $pt = new PluginTester();
        $pt->setContainer($this->container);
        $pt->setData('test.data');
        
        $repo = $this->em->getRepository('LOCKSSOMaticPluginBundle:LomPluginData');
        $pluginData = $repo->findOneBy(array(
            'datakey' => 'test.data'
        ));
        $this->assertNotNull($pluginData);
        $this->assertNull($pluginData->getDomain());
        $this->assertNull($pluginData->getObjectId());
        $this->assertNotNull($pluginData->getValue()); // serialized null isn't null!
        $this->em->remove($pluginData);
        $this->em->flush();
    }

    /**
     * Test getting some plugin data.
     */
    public function testGetPluginData()
    {
        $pt = new PluginTester();
        $pt->setContainer($this->container);
        $pt->setData('test.data.1');

        $repo = $this->em->getRepository('LOCKSSOMaticPluginBundle:LomPluginData');

        $data = $pt->getData('test.data.1');
        $this->assertNull($data);

        $pt->setData('test.data.2', null, array('foo' => 4));
        $data = $pt->getData('test.data.2');
        $this->assertEquals(array('foo' => 4), $data);

        foreach($repo->findAll() as $p) {
            $this->em->remove($p);
        }
        $this->em->flush();
    }

    /**
     * Test getting/setting data with a class name.
     */
    public function testGetDomainData()
    {
        $pt = new PluginTester();
        $pt->setContainer($this->container);
        $repo = $this->em->getRepository('LOCKSSOMaticPluginBundle:LomPluginData');
        $pt->setData('test.data.3', get_class($this));

        $data = $pt->getData('test.data.3', get_class($this));
        $this->assertNull($data);

        $pt->setData('test.data.4', get_class($this), array('foo' => 4));
        $data = $pt->getData('test.data.4', get_class($this));
        $this->assertEquals(array('foo' => 4), $data);

        foreach($repo->findAll() as $p) {
            $this->em->remove($p);
        }
        $this->em->flush();
    }

    /**
     * Test getting data with a persisted object.
     */
    public function testGetObjectData() {
        $lockssPlugin = new Plugins();
        $lockssPlugin->setName('chicanery');
        $this->em->persist($lockssPlugin);
        $this->em->flush();

        $pt = new PluginTester();
        $pt->setContainer($this->container);
        $repo = $this->em->getRepository('LOCKSSOMaticPluginBundle:LomPluginData');
        $pt->setData('test.data.5', $lockssPlugin);

        $data = $pt->getData('test.data.5', $lockssPlugin);
        $this->assertNull($data);

        $pt->setData('test.data.6', $lockssPlugin, array('foo' => 4));
        $data = $pt->getData('test.data.6', $lockssPlugin);
        $this->assertEquals(array('foo' => 4), $data);

        foreach($repo->findAll() as $p) {
            $this->em->remove($p);
        }
        $this->em->remove($lockssPlugin);
        $this->em->flush();
    }

    /**
     * Test setting data with a class name.
     */
    public function testSetDomainData()
    {
        $pt = new PluginTester();
        $pt->setContainer($this->container);
        $repo = $this->em->getRepository('LOCKSSOMaticPluginBundle:LomPluginData');

        $pt->setData('foobarbaz', get_class($this));
        $pluginData = $repo->findOneBy(array(
            'datakey' => 'foobarbaz'
        ));
        $this->assertNotNull($pluginData);
        $this->assertNotNull($pluginData->getDomain());
        $this->assertEquals(get_class($this), $pluginData->getDomain());
        $this->assertNull($pluginData->getObjectId());
        $this->assertNotNull($pluginData->getValue()); // serialized null isn't null!

        $this->em->remove($pluginData);
        $this->em->flush();
    }

    /**
     * Test setting data associated with an object.
     */
    public function testSetObjectData()
    {
        // build a phony Plugins object for testing.
        $lockssPlugin = new Plugins();
        $lockssPlugin->setName('chicanery');
        $this->em->persist($lockssPlugin);
        $this->em->flush();
        
        $pt = new PluginTester();
        $pt->setContainer($this->container);
        $repo = $this->em->getRepository('LOCKSSOMaticPluginBundle:LomPluginData');

        $pt->setData('frobinicate', $lockssPlugin);
        $pluginData = $repo->findOneBy(array(
            'datakey' => 'frobinicate'
        ));
        $this->assertNotNull($pluginData);
        $this->assertNotNull($pluginData->getDomain());
        $this->assertEquals(get_class($lockssPlugin), $pluginData->getDomain());
        $this->assertNotNull($pluginData->getObjectId());
        $this->assertNotNull($pluginData->getValue()); // serialized null isn't null!

        $this->em->remove($pluginData);
        $this->em->remove($lockssPlugin);
        $this->em->flush();
    }

    /**
     * Test setting complex data associated with an object.
     */
    public function testSetObjectWithData()
    {
        // build a phony Plugins object for testing.
        $lockssPlugin = new Plugins();
        $lockssPlugin->setName('sundries-etc');
        $this->em->persist($lockssPlugin);
        $this->em->flush();

        $pt = new PluginTester();
        $pt->setContainer($this->container);
        $repo = $this->em->getRepository('LOCKSSOMaticPluginBundle:LomPluginData');

        $pt->setData('frobinicate', $lockssPlugin, array('foo' => 'bar', 'yes' => 'no'));
        $pluginData = $repo->findOneBy(array(
            'datakey' => 'frobinicate'
        ));
        $this->assertNotNull($pluginData);
        $this->assertNotNull($pluginData->getDomain());
        $this->assertEquals(get_class($lockssPlugin), $pluginData->getDomain());
        $this->assertNotNull($pluginData->getObjectId());
        $this->assertEquals(array('foo' => 'bar', 'yes' => 'no'), unserialize($pluginData->getValue())); // serialized null isn't null!

        $this->em->remove($pluginData);
        $this->em->remove($lockssPlugin);
        $this->em->flush();
    }
}
