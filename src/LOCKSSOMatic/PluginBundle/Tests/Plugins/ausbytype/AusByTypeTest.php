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

namespace LOCKSSOMatic\PluginBundle\Tests\Plugins\ausbytype;

use Doctrine\ORM\EntityManager;
use J20\Uuid\Uuid;
use LOCKSSOMatic\CRUDBundle\Entity\ContentProviders;
use LOCKSSOMatic\CRUDBundle\Entity\Deposits;
use LOCKSSOMatic\PluginBundle\Event\DepositContentEvent;
use LOCKSSOMatic\PluginBundle\Event\ServiceDocumentEvent;
use LOCKSSOMatic\PluginBundle\Plugins\ausbysize\AusBySize;
use LOCKSSOMatic\PluginBundle\Plugins\ausbytype\AusByType;
use LOCKSSOMatic\PluginBundle\Plugins\ausbyyear\AusByYear;
use LOCKSSOMatic\SWORDBundle\Utilities\Namespaces;
use SimpleXMLElement;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;

/**
 * Test the AuByYear plugin.
 */
class AusByTypeTest extends KernelTestCase
{

    /** @var Container */
    private $container;
    
    private $providerId;
    
    /** @var EntityManager */
    private $em;

    /**
     * Create a new content provider and persist it to the database. The
     * provider's uuid is stored in $providerId.
     */
    public function setUp()
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
        $this->em->persist($provider);
        $this->em->flush();
        $this->providerId = $provider->getUuid();
    }

    /**
     * Remove the content provider and all the entities it refers to.
     */
    public function tearDown()
    {
        $provider = $this->em->getRepository('LOCKSSOMaticCRUDBundle:ContentProviders')
            ->findOneBy(array('uuid' => $this->providerId));
        
        foreach($provider->getDeposits() as $deposit) {
            $this->em->refresh($deposit);
            foreach($deposit->getContent() as $content) {
                $this->em->refresh($content);
                $this->em->remove($content);
            }
            $this->em->remove($deposit);
        }
        foreach($provider->getAus() as $au) {
            $this->em->remove($au);
        }
        $this->em->remove($provider);
        $this->em->flush();
    }

    /**
     * This test requires a kernel, entity manager, and a container so create them.
     * The container is the important part, as it provides the plugin service.
     */
    public function __construct()
    {
        parent::__construct();
        static::bootKernel();
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $this->container = static::$kernel->getContainer();
    }

    /**
     * Test getting the plugin from the service container.
     */
    public function testServiceContainer()
    {
        /** @var AusByYear */
        $plugin = $this->container->get('lomplugin.aus.type');
        $this->assertNotNull($plugin);
        $this->assertInstanceOf(
            'LOCKSSOMatic\PluginBundle\Plugins\AbstractPlugin',
            $plugin);
        $this->assertInstanceOf(
            'LOCKSSOMatic\PluginBundle\Plugins\ausbytype\AusByType',
            $plugin);
        $this->assertEquals('lomplugin.aus.type', $plugin->getPluginId());
    }

    /**
     * Test that the plugin provides a description of itself for the 
     * service document.
     */
    public function testOnServiceDocument()
    {
        $ns = new Namespaces();
        $xml = new SimpleXMLElement('<root />');
        $ns->registerNamespaces($xml);

        /** @var AusBySize */
        $plugin = $this->container->get('lomplugin.aus.type');
        $event = new ServiceDocumentEvent($xml);
        $plugin->onServiceDocument($event);

        $nodes = $xml->xpath('//lom:plugin');
        $this->assertEquals(1, count($nodes));
        $node = $nodes[0];
        $this->assertEquals('lomplugin.aus.type', (string)$node['pluginId']);
        $this->assertEquals('size, mimetype', (string)$node['attributes']);
    }

    /**
     * Attempt to deposit a single content item.
     */
    public function testOnDepositSingleContentExactMatch()
    {
        $provider = $this->em->getRepository('LOCKSSOMaticCRUDBundle:ContentProviders')
            ->findOneBy(array('uuid' => $this->providerId));
        
        $xml = new SimpleXMLElement('<lom:content mimetype="text/html" xmlns:lom="http://lockssomatic.info/SWORD2" year="2010" size="10" checksumType="md5" checksumValue="bd4a9b642562547754086de2dab26b7d">http://provider.example.com/download/file1.zip</lom:content>');
        $deposit = new Deposits();
        $deposit->setContentProvider($provider);
        $deposit->setUuid(Uuid::v4());
        $deposit->setTitle('Test deposit abst-todsc ');
        $this->em->persist($deposit);
        $event = new DepositContentEvent('lomplugin.aus.type', $deposit, $provider, $xml);

        /** @var AusByType */
        $plugin = $this->container->get('lomplugin.aus.type');

        $plugin->onDepositContent($event);
        $this->em->refresh($provider);
        $this->assertEquals(1, $provider->getAus()->count());
        $data = $plugin->getData('AuParams', $provider->getAus()->first());
        $this->assertEquals(array('ByType' => true, 'type' => 'text/html'), $data);
    }

    /**
     * Attempt to deposit a single content item.
     */
    public function testOnDepositSingleContentLooseMatch()
    {
        $provider = $this->em->getRepository('LOCKSSOMaticCRUDBundle:ContentProviders')
            ->findOneBy(array('uuid' => $this->providerId));
        
        $xml = new SimpleXMLElement('<lom:content mimetype="text/foooooo" xmlns:lom="http://lockssomatic.info/SWORD2" year="2010" size="10" checksumType="md5" checksumValue="bd4a9b642562547754086de2dab26b7d">http://provider.example.com/download/file1.zip</lom:content>');
        $deposit = new Deposits();
        $deposit->setContentProvider($provider);
        $deposit->setUuid(Uuid::v4());
        $deposit->setTitle('Test deposit abst-todsc ');
        $this->em->persist($deposit);
        $event = new DepositContentEvent('lomplugin.aus.type', $deposit, $provider, $xml);

        /** @var AusByType */
        $plugin = $this->container->get('lomplugin.aus.type');

        $plugin->onDepositContent($event);
        $this->em->refresh($provider);
        $this->assertEquals(1, $provider->getAus()->count());
        $data = $plugin->getData('AuParams', $provider->getAus()->first());
        $this->assertEquals(array('ByType' => true, 'type' => 'text/*'), $data);
    }

    /**
     * Attempt to deposit a single content item.
     */
    public function testOnDepositSingleContentNoMatch()
    {
        $provider = $this->em->getRepository('LOCKSSOMaticCRUDBundle:ContentProviders')
            ->findOneBy(array('uuid' => $this->providerId));
        
        $xml = new SimpleXMLElement('<lom:content mimetype="blammo/foooooo" xmlns:lom="http://lockssomatic.info/SWORD2" year="2010" size="10" checksumType="md5" checksumValue="bd4a9b642562547754086de2dab26b7d">http://provider.example.com/download/file1.zip</lom:content>');
        $deposit = new Deposits();
        $deposit->setContentProvider($provider);
        $deposit->setUuid(Uuid::v4());
        $deposit->setTitle('Test deposit abst-todsc ');
        $this->em->persist($deposit);
        $event = new DepositContentEvent('lomplugin.aus.type', $deposit, $provider, $xml);

        /** @var AusByType */
        $plugin = $this->container->get('lomplugin.aus.type');

        $plugin->onDepositContent($event);
        $this->em->refresh($provider);
        $this->assertEquals(1, $provider->getAus()->count());
        $data = $plugin->getData('AuParams', $provider->getAus()->first());
        $this->assertEquals(array('ByType' => true, 'type' => '*/*'), $data);
    }

}
