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

namespace LOCKSSOMatic\PluginBundle\Plugins\ausbysize;

use LOCKSSOMatic\CRUDBundle\Entity\Aus;
use LOCKSSOMatic\CRUDBundle\Entity\ContentBuilder;
use LOCKSSOMatic\CRUDBundle\Entity\ContentProviders;
use LOCKSSOMatic\PluginBundle\Event\DepositContentEvent;
use LOCKSSOMatic\PluginBundle\Event\ServiceDocumentEvent;
use LOCKSSOMatic\PluginBundle\Plugins\AbstractPlugin;
use LOCKSSOMatic\SWORDBundle\Utilities\Namespaces;
use SimpleXMLElement;

/**
 * Organize AUs by size.
 */
class AusBySize extends AbstractPlugin
{

    /**
     * This method is automatically called when a service document
     * is requested.
     * 
     * @param ServiceDocumentEvent $event
     */
    public function onServiceDocument(ServiceDocumentEvent $event)
    {
        /** @var SimpleXMLElement */
        $xml = $event->getXml();
        $plugin = $xml->addChild('plugin', null, Namespaces::LOM);
        $plugin->addAttribute('attributes', 'size');
        $plugin->addAttribute('pluginId', $this->getPluginId());
    }

    /**
     * Called automatically when content is deposited. Finds or creates an
     * AU and adds the newly created content item to it.
     * 
     * @param DepositContentEvent $event
     */
    public function onDepositContent(DepositContentEvent $event)
    {
        /** @var ContentProviders */
        $contentProvider = $event->getContentProvider();
        $deposit = $event->getDeposit();
        $contentXml = $event->getXml();

        $maxSize = $contentProvider->getMaxAuSize();
        $contentSize = (string)$contentXml->attributes()->size;

        // hack around a PHP 5.3 bug.
        $self = $this;

        $filter = function(Aus $au) use($self, $maxSize, $contentSize) {
            if ($au->getContentSize() + $contentSize >= $maxSize) {
                return false;
            }
            $data = $self->getData('AuParams', $au);
            if ($data === null) {
                return false;
            }
            return true;
        };

        $this->container->get('doctrine')->getManager()->refresh($contentProvider);
        $aus = $contentProvider->getAus()->filter($filter);
        if ($aus->count() >= 1) {
            $au = $aus->first();
        } else {
            $au = new Aus();
            $au->setContentProvider($contentProvider);
            $au->setManaged(true);
            $au->setAuid('some generated auid - size - odc');
            $au->setManifestUrl('http://pln.example.com/foo/bar');
            $this->container->get('doctrine')->getManager()->persist($au);
            $this->container->get('doctrine')->getManager()->flush();
            $this->setData('AuParams', $au, array('ByYear' => true));
        }
        $contentBuilder = new ContentBuilder();
        $content = $contentBuilder->fromSimpleXML($contentXml);
        $content->setDeposit($deposit);
        $content->setAu($au);
        $this->container->get('doctrine')->getManager()->persist($content);
        $au->addContent($content);
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription()
    {
        return "Organize archival units by size.";
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return "AUsBySize";
    }

}
