<?php

namespace LOCKSSOMatic\PluginBundle\Tests\Plugins\ausbysize;

use J20\Uuid\Uuid;
use LOCKSSOMatic\CRUDBundle\Entity\ContentProviders;
use LOCKSSOMatic\CRUDBundle\Entity\Deposits;
use LOCKSSOMatic\PluginBundle\Event\DepositContentEvent;
use LOCKSSOMatic\PluginBundle\Event\ServiceDocumentEvent;
use LOCKSSOMatic\PluginBundle\Plugins\ausbysize\AusBySize;
use LOCKSSOMatic\SWORDBundle\Utilities\Namespaces;
use SimpleXMLElement;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;

class AusBySizeTest extends KernelTestCase
{

    /** @var Container */
    private $container;
    
    private static $provider;
    
    private static $em;

    // called before the the class is run.
    public static function setUpBeforeClass()
    {
        $provider = new ContentProviders();
        $provider->setType('test');
        $provider->setName('Test provider 1');
        $provider->setIpAddress('127.0.0.1');
        $provider->setHostname('provider.example.com');
        $provider->setChecksumType('md5');
        $provider->setMaxFileSize('8000'); // in kB
        $provider->setMaxAuSize('10000'); // also in kB
        $provider->setPermissionUrl('http://provider.example.com/path/to/permissions');
        static::$em->persist($provider);
        static::$em->flush();
        static::$provider = $provider;
    }

    public static function tearDownAfterClass()
    {
        static::$em->refresh(self::$provider);
        foreach(static::$provider->getDeposits() as $deposit) {
            static::$em->refresh($deposit);
            foreach($deposit->getContent() as $content) {
                static::$em->refresh($content);
                static::$em->remove($content);
            }
            static::$em->remove($deposit);
        }
        foreach(static::$provider->getAus() as $au) {
            static::$em->remove($au);
        }
        static::$em->remove(static::$provider);
        static::$em->flush();
    }

    public function __construct()
    {
        parent::__construct();
        static::bootKernel();
        static::$em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $this->container = static::$kernel->getContainer();
    }

    public function testServiceContainer()
    {
        $plugin = $this->container->get('lomplugin.aus.size');
        $this->assertNotNull($plugin);
        $this->assertInstanceOf('LOCKSSOMatic\PluginBundle\Plugins\AbstractPlugin',
            $plugin);
        $this->assertInstanceOf('LOCKSSOMatic\PluginBundle\Plugins\ausbysize\AusBySize',
            $plugin);
    }

    public function testOnServiceDocument()
    {
        $ns = new Namespaces();
        $xml = new SimpleXMLElement('<root />');
        $ns->registerNamespaces($xml);

        /** @var AusBySize */
        $plugin = $this->container->get('lomplugin.aus.size');

        $event = new ServiceDocumentEvent($xml);
        $plugin->onServiceDocument($event);

        $nodes = $xml->xpath('//lom:plugin');
        $this->assertEquals(1, count($nodes));

        $node = $nodes[0];
        $this->assertEquals(get_class($plugin), $node['name']);
        $this->assertEquals('size', $node['attributes']);
    }

//    public function testOnDepositSingleContent()
//    {
//        $provider = static::$provider;
//        $xml = new SimpleXMLElement('<lom:content xmlns:lom="http://lockssomatic.info/SWORD2" plugin="LOCKSSOMatic\PluginBundle\Plugins\ausbysize\AusBySize" size="10" checksumType="md5" checksumValue="bd4a9b642562547754086de2dab26b7d">http://provider.example.com/download/file1.zip</lom:content>');
//        $event = new DepositContentEvent($provider, $xml);
//
//        /** @var AusBySize */
//        $plugin = $this->container->get('lomplugin.aus.size');
//
//        $plugin->onDepositContent($event);
//        self::$em->refresh($provider);
//        $this->assertEquals(1, $provider->getAus()->count());
//    }

    // All of these content items should go in the same au.
    public function testOnDepositSingleAu()
    {
        $provider = static::$provider;
        $items = array(
            '<lom:content xmlns:lom="http://lockssomatic.info/SWORD2" plugin="LOCKSSOMatic\PluginBundle\Plugins\ausbysize\AusBySize" size="4000" checksumType="md5" checksumValue="bd4a9b642562547754086de2dab26b7d">http://provider.example.com/download/file1.zip</lom:content>',
            '<lom:content xmlns:lom="http://lockssomatic.info/SWORD2" plugin="LOCKSSOMatic\PluginBundle\Plugins\ausbysize\AusBySize" size="1000" checksumType="md5" checksumValue="bd4a9b642562547754086de2dab26b7d">http://provider.example.com/download/file1.zip</lom:content>',
            '<lom:content xmlns:lom="http://lockssomatic.info/SWORD2" plugin="LOCKSSOMatic\PluginBundle\Plugins\ausbysize\AusBySize" size="2000" checksumType="md5" checksumValue="bd4a9b642562547754086de2dab26b7d">http://provider.example.com/download/file1.zip</lom:content>',
        );
        
        /** @var AusBySize */
        $plugin = $this->container->get('lomplugin.aus.size');
        $deposit = new Deposits();
        $deposit->setContentProvider(static::$provider);
        $deposit->setUuid(Uuid::v4());
        $deposit->setTitle('Test deposit abst-todsa');
        static::$em->persist($deposit);
        static::$em->flush();

        foreach($items as $item) {
            $xml = new SimpleXMLElement($item);        
            $event = new DepositContentEvent($deposit, $provider, $xml);
            $plugin->onDepositContent($event);
        }

        self::$em->refresh($provider);
        $this->assertEquals(1, $provider->getAus()->count());
    }

}
