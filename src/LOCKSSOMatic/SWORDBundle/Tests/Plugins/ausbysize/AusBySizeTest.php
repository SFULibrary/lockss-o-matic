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

use J20\Uuid\Uuid;
use LOCKSSOMatic\CRUDBundle\Entity\Deposits;
use LOCKSSOMatic\SWORDBundle\Event\DepositContentEvent;
use LOCKSSOMatic\SWORDBundle\Event\ServiceDocumentEvent;
use LOCKSSOMatic\SWORDBundle\Plugins\ausbysize\AusBySize;
use LOCKSSOMatic\SWORDBundle\Tests\Plugins\TestCases\DepositTestCase;
use LOCKSSOMatic\SWORDBundle\Utilities\Namespaces;
use SimpleXMLElement;

/**
 * Test the AuBySize plugin.
 */
class AusBySizeTest extends DepositTestCase
{
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
