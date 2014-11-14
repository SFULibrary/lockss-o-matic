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

namespace LOCKSSOMatic\SWORDBundle\Tests\Plugins\ausbysize;

use Doctrine\ORM\EntityManager;
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

/**
 * Test the AuBySize plugin.
 */
class AusBySizeTest extends KernelTestCase
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
        $plugin = $this->container->get('lomplugin.aus.size');
        $this->assertNotNull($plugin);
        $this->assertInstanceOf('LOCKSSOMatic\PluginBundle\Plugins\AbstractPlugin',
            $plugin);
        $this->assertInstanceOf('LOCKSSOMatic\SWORDBundle\Plugins\ausbysize\AusBySize',
            $plugin);
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
        $plugin = $this->container->get('lomplugin.aus.size');
        $event = new ServiceDocumentEvent($xml);
        $plugin->onServiceDocument($event);

        $nodes = $xml->xpath('//lom:plugin');
        $this->assertEquals(1, count($nodes));
        $node = $nodes[0];
        $this->assertEquals('lomplugin.aus.size', $node['pluginId']);
        $this->assertEquals('size', $node['attributes']);
    }

    /**
     * Attempt to deposit a single content item.
     */
    public function testOnDepositSingleContent()
    {
        $provider = $this->em->getRepository('LOCKSSOMaticCRUDBundle:ContentProviders')
            ->findOneBy(array('uuid' => $this->providerId));
        
        $xml = new SimpleXMLElement('<lom:content xmlns:lom="http://lockssomatic.info/SWORD2" plugin="LOCKSSOMatic\PluginBundle\Plugins\ausbysize\AusBySize" size="10" checksumType="md5" checksumValue="bd4a9b642562547754086de2dab26b7d">http://provider.example.com/download/file1.zip</lom:content>');
        $deposit = new Deposits();
        $deposit->setContentProvider($provider);
        $deposit->setUuid(Uuid::v4());
        $deposit->setTitle('Test deposit abst-todsc ');
        $this->em->persist($deposit);
        $event = new DepositContentEvent('lomplugin.aus.size', $deposit, $provider, $xml);

        /** @var AusBySize */
        $plugin = $this->container->get('lomplugin.aus.size');

        $plugin->onDepositContent($event);
        $this->em->refresh($provider);
        $this->assertEquals(1, $provider->getAus()->count());
    }

    /**
     * Attempt to deposit multiple content items - they should all go to to the
     * same AU, because they fit.
     */
    public function testOnDepositSingleAu()
    {
        $provider = $this->em->getRepository('LOCKSSOMaticCRUDBundle:ContentProviders')
            ->findOneBy(array('uuid' => $this->providerId));
        
        $items = array(
            '<lom:content xmlns:lom="http://lockssomatic.info/SWORD2" plugin="LOCKSSOMatic\PluginBundle\Plugins\ausbysize\AusBySize" size="4000" checksumType="md5" checksumValue="bd4a9b642562547754086de2dab26b7d">http://provider.example.com/download/file1.zip</lom:content>',
            '<lom:content xmlns:lom="http://lockssomatic.info/SWORD2" plugin="LOCKSSOMatic\PluginBundle\Plugins\ausbysize\AusBySize" size="1000" checksumType="md5" checksumValue="bd4a9b642562547754086de2dab26b7d">http://provider.example.com/download/file1.zip</lom:content>',
            '<lom:content xmlns:lom="http://lockssomatic.info/SWORD2" plugin="LOCKSSOMatic\PluginBundle\Plugins\ausbysize\AusBySize" size="2000" checksumType="md5" checksumValue="bd4a9b642562547754086de2dab26b7d">http://provider.example.com/download/file1.zip</lom:content>',
        );
        
        /** @var AusBySize */
        $plugin = $this->container->get('lomplugin.aus.size');
        $deposit = new Deposits();
        $deposit->setContentProvider($provider);
        $deposit->setUuid(Uuid::v4());
        $deposit->setTitle('Test deposit abst-todsa');
        $this->em->persist($deposit);
        $this->em->flush();

        foreach($items as $item) {
            $xml = new SimpleXMLElement($item);        
            $event = new DepositContentEvent('lomplugin.aus.size', $deposit, $provider, $xml);
            $plugin->onDepositContent($event);
        }

        $this->em->refresh($provider);
        $this->assertEquals(1, $provider->getAus()->count());
    }

    /**
     * Attempt to deposit multiple content items. They don't fit in a single AU.
     */
    public function testOnDepositMultipleAus()
    {
        $provider = $this->em->getRepository('LOCKSSOMaticCRUDBundle:ContentProviders')
            ->findOneBy(array('uuid' => $this->providerId));
        
        // These should go into three AUs, based on size.
        $items = array(
            '<lom:content xmlns:lom="http://lockssomatic.info/SWORD2" plugin="LOCKSSOMatic\PluginBundle\Plugins\ausbysize\AusBySize" size="4000" checksumType="md5" checksumValue="bd4a9b642562547754086de2dab26b7d">http://provider.example.com/download/file1.zip</lom:content>',
            '<lom:content xmlns:lom="http://lockssomatic.info/SWORD2" plugin="LOCKSSOMatic\PluginBundle\Plugins\ausbysize\AusBySize" size="1000" checksumType="md5" checksumValue="bd4a9b642562547754086de2dab26b7d">http://provider.example.com/download/file1.zip</lom:content>',
            '<lom:content xmlns:lom="http://lockssomatic.info/SWORD2" plugin="LOCKSSOMatic\PluginBundle\Plugins\ausbysize\AusBySize" size="2000" checksumType="md5" checksumValue="bd4a9b642562547754086de2dab26b7d">http://provider.example.com/download/file1.zip</lom:content>',
            '<lom:content xmlns:lom="http://lockssomatic.info/SWORD2" plugin="LOCKSSOMatic\PluginBundle\Plugins\ausbysize\AusBySize" size="4000" checksumType="md5" checksumValue="bd4a9b642562547754086de2dab26b7d">http://provider.example.com/download/file1.zip</lom:content>',
            '<lom:content xmlns:lom="http://lockssomatic.info/SWORD2" plugin="LOCKSSOMatic\PluginBundle\Plugins\ausbysize\AusBySize" size="1000" checksumType="md5" checksumValue="bd4a9b642562547754086de2dab26b7d">http://provider.example.com/download/file1.zip</lom:content>',
            '<lom:content xmlns:lom="http://lockssomatic.info/SWORD2" plugin="LOCKSSOMatic\PluginBundle\Plugins\ausbysize\AusBySize" size="2000" checksumType="md5" checksumValue="bd4a9b642562547754086de2dab26b7d">http://provider.example.com/download/file1.zip</lom:content>',
            '<lom:content xmlns:lom="http://lockssomatic.info/SWORD2" plugin="LOCKSSOMatic\PluginBundle\Plugins\ausbysize\AusBySize" size="4000" checksumType="md5" checksumValue="bd4a9b642562547754086de2dab26b7d">http://provider.example.com/download/file1.zip</lom:content>',
            '<lom:content xmlns:lom="http://lockssomatic.info/SWORD2" plugin="LOCKSSOMatic\PluginBundle\Plugins\ausbysize\AusBySize" size="1000" checksumType="md5" checksumValue="bd4a9b642562547754086de2dab26b7d">http://provider.example.com/download/file1.zip</lom:content>',
            '<lom:content xmlns:lom="http://lockssomatic.info/SWORD2" plugin="LOCKSSOMatic\PluginBundle\Plugins\ausbysize\AusBySize" size="2000" checksumType="md5" checksumValue="bd4a9b642562547754086de2dab26b7d">http://provider.example.com/download/file1.zip</lom:content>',
        );
        
        /** @var AusBySize */
        $plugin = $this->container->get('lomplugin.aus.size');
        $deposit = new Deposits();
        $deposit->setContentProvider($provider);
        $deposit->setUuid(Uuid::v4());
        $deposit->setTitle('Test deposit abst-todsa');
        $this->em->persist($deposit);
        $this->em->flush();

        foreach($items as $item) {
            $xml = new SimpleXMLElement($item);        
            $event = new DepositContentEvent('lomplugin.aus.size', $deposit, $provider, $xml);
            $plugin->onDepositContent($event);
        }

        $this->em->refresh($provider);
        $this->assertEquals(3, $provider->getAus()->count());
    }

}
