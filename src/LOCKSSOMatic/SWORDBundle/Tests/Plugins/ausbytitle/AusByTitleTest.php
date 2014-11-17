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

namespace LOCKSSOMatic\SWORDBundle\Tests\Plugins\ausbytitle;

use J20\Uuid\Uuid;
use LOCKSSOMatic\CRUDBundle\Entity\Deposits;
use LOCKSSOMatic\SWORDBundle\Event\DepositContentEvent;
use LOCKSSOMatic\SWORDBundle\Event\ServiceDocumentEvent;
use LOCKSSOMatic\SWORDBundle\Plugins\ausbytitle\AusByTitle;
use LOCKSSOMatic\SWORDBundle\Tests\Plugins\TestCases\DepositTestCase;
use LOCKSSOMatic\SWORDBundle\Utilities\Namespaces;
use SimpleXMLElement;

/**
 * Test the AuByTitle plugin.
 */
class AusByTitleTest extends DepositTestCase
{

    /**
     * Test getting the plugin from the service container.
     */
    public function testServiceContainer()
    {
        /** @var AuByTitle */
        $plugin = $this->container->get('lomplugin.aus.title');
        $this->assertNotNull($plugin);
        $this->assertInstanceOf(
            'LOCKSSOMatic\PluginBundle\Plugins\AbstractPlugin', $plugin);
        $this->assertInstanceOf(
            'LOCKSSOMatic\SWORDBundle\Plugins\ausbytitle\AusByTitle', $plugin);
        $this->assertEquals('lomplugin.aus.title', $plugin->getPluginId());
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

        /** @var AusByTitle */
        $plugin = $this->container->get('lomplugin.aus.title');
        $plugin->loadSettings(__DIR__ . '/settings.yml');

        $event = new ServiceDocumentEvent($xml);
        $plugin->onServiceDocument($event);

        $nodes = $xml->xpath('//lom:plugin');
        $this->assertEquals(1, count($nodes));
        $node = $nodes[0];
        $this->assertEquals('lomplugin.aus.title', (string) $node['pluginId']);
        $this->assertEquals('size, title', (string) $node['attributes']);
    }

    /**
     * Attempt to deposit a single content item.
     */
    public function testOnDepositTest()
    {
        $provider = $this->em->getRepository('LOCKSSOMaticCRUDBundle:ContentProviders')
            ->findOneBy(array('uuid' => $this->providerId));

        $items = array(
            '<lom:content xmlns:lom="http://lockssomatic.info/SWORD2"  title="A big title" size="10" checksumType="md5" checksumValue="bd4a9b642562547754086de2dab26b7d">http://provider.example.com/download/file1.zip</lom:content>',
            '<lom:content xmlns:lom="http://lockssomatic.info/SWORD2"  title="Bigger title" size="10" checksumType="md5" checksumValue="bd4a9b642562547754086de2dab26b7d">http://provider.example.com/download/file1.zip</lom:content>',
            '<lom:content xmlns:lom="http://lockssomatic.info/SWORD2"  title="Road crossings by chickens, circa 1992" size="10" checksumType="md5" checksumValue="bd4a9b642562547754086de2dab26b7d">http://provider.example.com/download/file1.zip</lom:content>',
            '<lom:content xmlns:lom="http://lockssomatic.info/SWORD2"  title="Silly jokes and other poems" size="5" checksumType="md5" checksumValue="226190d94b21d1b0c7b1a42d855e419d">http://provider.example.com/download/file2.zip</lom:content>',
            '<lom:content xmlns:lom="http://lockssomatic.info/SWORD2"  title="23 other poems" size="5" checksumType="md5" checksumValue="226190d94b21d1b0c7b1a42d855e419d">http://provider.example.com/download/file2.zip</lom:content>',
            '<lom:content xmlns:lom="http://lockssomatic.info/SWORD2"  title=\'"Quotes in Titles" and other works of art\' size="10" checksumType="md5" checksumValue="bd4a9b642562547754086de2dab26b7d">http://provider.example.com/download/file1.zip</lom:content>',
            '<lom:content xmlns:lom="http://lockssomatic.info/SWORD2"  title=".com and other domains" size="10" checksumType="md5" checksumValue="bd4a9b642562547754086de2dab26b7d">http://provider.example.com/download/file1.zip</lom:content>',
        );

        /** @var AusByTitle */
        $plugin = $this->container->get('lomplugin.aus.title');
        $deposit = new Deposits();
        $deposit->setContentProvider($provider);
        $deposit->setUuid(Uuid::v4());
        $deposit->setTitle('Test deposit abst-todsa');
        $this->em->persist($deposit);
        $this->em->flush();

        foreach ($items as $item) {
            $xml = new SimpleXMLElement($item);
            $event = new DepositContentEvent('lomplugin.aus.title', $deposit, $provider, $xml);
            $plugin->onDepositContent($event);
        }

        $this->em->refresh($provider);
        $this->assertEquals(4, $provider->getAus()->count());
    }

}
