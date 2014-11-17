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

namespace LOCKSSOMatic\SWORDBundle\Tests\Plugins\ausbytype;

use J20\Uuid\Uuid;
use LOCKSSOMatic\CRUDBundle\Entity\Deposits;
use LOCKSSOMatic\SWORDBundle\Event\DepositContentEvent;
use LOCKSSOMatic\SWORDBundle\Event\ServiceDocumentEvent;
use LOCKSSOMatic\SWORDBundle\Plugins\ausbytype\AusByType;
use LOCKSSOMatic\SWORDBundle\Tests\Plugins\TestCases\DepositTestCase;
use LOCKSSOMatic\SWORDBundle\Utilities\Namespaces;
use SimpleXMLElement;

/**
 * Test the AuByType plugin.
 */
class AusByTypeTest extends DepositTestCase
{

    /**
     * Test getting the plugin from the service container.
     */
    public function testServiceContainer()
    {
        /** @var AuByType */
        $plugin = $this->container->get('lomplugin.aus.type');
        $this->assertNotNull($plugin);
        $this->assertInstanceOf(
            'LOCKSSOMatic\PluginBundle\Plugins\AbstractPlugin',
            $plugin);
        $this->assertInstanceOf(
            'LOCKSSOMatic\SWORDBundle\Plugins\ausbytype\AusByType',
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

        /** @var AusByType */
        $plugin = $this->container->get('lomplugin.aus.type');
        $plugin->loadSettings(__DIR__ . '/settings.yml');

        $event = new ServiceDocumentEvent($xml);
        $plugin->onServiceDocument($event);

        $nodes = $xml->xpath('//lom:plugin');
        $this->assertEquals(1, count($nodes));
        $node = $nodes[0];
        $this->assertEquals('lomplugin.aus.type', (string)$node['pluginId']);
        $this->assertEquals('mimetype, size', (string)$node['attributes']);
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
        $this->assertEquals(array('ByType' => true, 'group' => 'text'), $data);
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
        $this->assertEquals(array('ByType' => true, 'group' => 'text'), $data);
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
        $this->assertEquals(array('ByType' => true, 'group' => 'other'), $data);
    }

}
