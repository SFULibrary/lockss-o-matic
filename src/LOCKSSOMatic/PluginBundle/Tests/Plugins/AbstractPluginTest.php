<?php

namespace LOCKSSOMatic\PluginBundle\Tests\Plugins;

use Doctrine\ORM\EntityManager;
use LOCKSSOMatic\CRUDBundle\Entity\Plugins;
use LOCKSSOMatic\PluginBundle\Tests\Plugins\PluginTester;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;

class AbstractPluginTest extends KernelTestCase
{

    /** @var Container */
    private $container;

    /** @var EntityManager */
    private static $em;

    // called before the the class is run.
    public static function setUpBeforeClass()
    {

    }

    public static function tearDownAfterClass()
    {

    }

    public function __construct()
    {
        parent::__construct();
        static::bootKernel();
        static::$em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $this->container = static::$kernel->getContainer();
    }

    public function testSetPluginData()
    {
        $pt = new PluginTester();
        $pt->setContainer($this->container);
        $repo = static::$em->getRepository('LOCKSSOMaticPluginBundle:LomPluginData');
        $pt->setData('test.data');
        $pluginData = $repo->findOneBy(array(
            'datakey' => 'test.data'
        ));
        $this->assertNotNull($pluginData);
        $this->assertNull($pluginData->getDomain());
        $this->assertNull($pluginData->getObjectId());
        $this->assertNotNull($pluginData->getValue()); // serialized null isn't null!
        static::$em->remove($pluginData);
        static::$em->flush();
    }

    public function testGetPluginData()
    {
        $pt = new PluginTester();
        $pt->setContainer($this->container);
        $repo = static::$em->getRepository('LOCKSSOMaticPluginBundle:LomPluginData');
        $pt->setData('test.data.1');

        $data = $pt->getData('test.data.1');
        $this->assertNull($data);

        $pt->setData('test.data.2', null, array('foo' => 4));
        $data = $pt->getData('test.data.2');
        $this->assertEquals(array('foo' => 4), $data);

        foreach($repo->findAll() as $p) {
            static::$em->remove($p);
        }
        static::$em->flush();
    }

    public function testGetDomainData()
    {
        $pt = new PluginTester();
        $pt->setContainer($this->container);
        $repo = static::$em->getRepository('LOCKSSOMaticPluginBundle:LomPluginData');
        $pt->setData('test.data.3', get_class($this));

        $data = $pt->getData('test.data.3', get_class($this));
        $this->assertNull($data);

        $pt->setData('test.data.4', get_class($this), array('foo' => 4));
        $data = $pt->getData('test.data.4', get_class($this));
        $this->assertEquals(array('foo' => 4), $data);

        foreach($repo->findAll() as $p) {
            static::$em->remove($p);
        }
        static::$em->flush();
    }

    public function testGetObjectData() {
        $lockssPlugin = new Plugins();
        $lockssPlugin->setName('chicanery');
        self::$em->persist($lockssPlugin);
        self::$em->flush();

        $pt = new PluginTester();
        $pt->setContainer($this->container);
        $repo = static::$em->getRepository('LOCKSSOMaticPluginBundle:LomPluginData');
        $pt->setData('test.data.5', $lockssPlugin);

        $data = $pt->getData('test.data.5', $lockssPlugin);
        $this->assertNull($data);

        $pt->setData('test.data.6', $lockssPlugin, array('foo' => 4));
        $data = $pt->getData('test.data.6', $lockssPlugin);
        $this->assertEquals(array('foo' => 4), $data);

        foreach($repo->findAll() as $p) {
            static::$em->remove($p);
        }
        static::$em->remove($lockssPlugin);
        static::$em->flush();
    }

    public function testSetDomainData()
    {
        $pt = new PluginTester();
        $pt->setContainer($this->container);
        $repo = static::$em->getRepository('LOCKSSOMaticPluginBundle:LomPluginData');

        $pt->setData('foobarbaz', get_class($this));
        $pluginData = $repo->findOneBy(array(
            'datakey' => 'foobarbaz'
        ));
        $this->assertNotNull($pluginData);
        $this->assertNotNull($pluginData->getDomain());
        $this->assertEquals(get_class($this), $pluginData->getDomain());
        $this->assertNull($pluginData->getObjectId());
        $this->assertNotNull($pluginData->getValue()); // serialized null isn't null!

        static::$em->remove($pluginData);
        static::$em->flush();
    }

    public function testSetObjectData()
    {
        // build a phony Plugins object for testing.
        $lockssPlugin = new Plugins();
        $lockssPlugin->setName('chicanery');
        self::$em->persist($lockssPlugin);
        self::$em->flush();
        
        $pt = new PluginTester();
        $pt->setContainer($this->container);
        $repo = static::$em->getRepository('LOCKSSOMaticPluginBundle:LomPluginData');

        $pt->setData('frobinicate', $lockssPlugin);
        $pluginData = $repo->findOneBy(array(
            'datakey' => 'frobinicate'
        ));
        $this->assertNotNull($pluginData);
        $this->assertNotNull($pluginData->getDomain());
        $this->assertEquals(get_class($lockssPlugin), $pluginData->getDomain());
        $this->assertNotNull($pluginData->getObjectId());
        $this->assertNotNull($pluginData->getValue()); // serialized null isn't null!

        static::$em->remove($pluginData);
        static::$em->remove($lockssPlugin);
        static::$em->flush();
    }

    public function testSetObjectWithData()
    {
        // build a phony Plugins object for testing.
        $lockssPlugin = new Plugins();
        $lockssPlugin->setName('sundries-etc');
        self::$em->persist($lockssPlugin);
        self::$em->flush();

        $pt = new PluginTester();
        $pt->setContainer($this->container);
        $repo = static::$em->getRepository('LOCKSSOMaticPluginBundle:LomPluginData');

        $pt->setData('frobinicate', $lockssPlugin, array('foo' => 'bar', 'yes' => 'no'));
        $pluginData = $repo->findOneBy(array(
            'datakey' => 'frobinicate'
        ));
        $this->assertNotNull($pluginData);
        $this->assertNotNull($pluginData->getDomain());
        $this->assertEquals(get_class($lockssPlugin), $pluginData->getDomain());
        $this->assertNotNull($pluginData->getObjectId());
        $this->assertEquals(array('foo' => 'bar', 'yes' => 'no'), unserialize($pluginData->getValue())); // serialized null isn't null!

        static::$em->remove($pluginData);
        static::$em->remove($lockssPlugin);
        static::$em->flush();
    }
}
