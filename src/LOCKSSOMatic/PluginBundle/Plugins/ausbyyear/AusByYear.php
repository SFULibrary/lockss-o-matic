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

namespace LOCKSSOMatic\PluginBundle\Plugins\ausbyyear;

use LOCKSSOMatic\CRUDBundle\Entity\Aus;
use LOCKSSOMatic\CRUDBundle\Entity\ContentBuilder;
use LOCKSSOMatic\CRUDBundle\Entity\ContentProviders;
use LOCKSSOMatic\PluginBundle\Event\DepositContentEvent;
use LOCKSSOMatic\PluginBundle\Event\ServiceDocumentEvent;
use LOCKSSOMatic\PluginBundle\Plugins\AbstractPlugin;
use LOCKSSOMatic\SWORDBundle\Utilities\Namespaces;
use SimpleXMLElement;

/**
 * Organize AUs by year published, while respecting the content provider's maximum
 * AU size.
 */
class AusByYear extends AbstractPlugin
{
    /**
     * Automatically called when a service document is requested.
     * 
     * @param ServiceDocumentEvent $event
     */
    public function onServiceDocument(ServiceDocumentEvent $event)
    {
        /** @var SimpleXMLElement */
        $xml = $event->getXml();
        $plugin = $xml->addChild('plugin', null, Namespaces::LOM);
        $plugin->addAttribute('attributes', 'year');
        $plugin->addAttribute('pluginId', $this->getPluginId());
    }

    /**
     * Called automatically when new content is deposited. Finds or creates an
     * Au based on the year and size of the content.
     * 
     * @param DepositContentEvent $event
     * @return Aus
     */
    public function onDepositContent(DepositContentEvent $event)
    {
        /** @var ContentProviders */
        $contentProvider = $event->getContentProvider();
        $deposit = $event->getDeposit();
        $contentXml = $event->getXml();

        $maxSize = $contentProvider->getMaxAuSize();
        $contentSize = (string) $contentXml->attributes()->size;
        $contentYear = (string) $contentXml->attributes()->year;

        $self = $this;
        $filter = function(Aus $au) use ($self, $maxSize, $contentSize, $contentYear) {
            if ($au->getContentSize() + $contentSize >= $maxSize) {
                return false;
            }
            $data = $self->getData('AuParams', $au);
            if ($data === false) {
                return false;
            }
            if ($data['ByYear'] === true && $data['year'] === $contentYear) {
                return true;
            }
            return false;
        };

        $this->container->get('doctrine')->getManager()->refresh($contentProvider);
        $aus = $contentProvider->getAus()->filter($filter);
        if ($aus->count() >= 1) {
            $au = $aus->first();
        } else {
            $au = new Aus();
            $au->setContentProvider($contentProvider);
            $au->setManaged(true);
            $au->setAuid('auid-year-abr-' . $contentYear);
            $au->setManifestUrl('http://pln.example.com/foo/year');
            $this->container->get('doctrine')->getManager()->persist($au);
            $this->container->get('doctrine')->getManager()->flush();
            $this->setData('AuParams', $au, array('ByYear' => true, 'year' => $contentYear));
        }
        $contentBuilder = new ContentBuilder();
        $content = $contentBuilder->fromSimpleXML($contentXml);
        $content->setDeposit($deposit);
        $content->setAu($au);
        $this->container->get('doctrine')->getManager()->persist($content);
        $au->addContent($content);
        $this->container->get('doctrine')->getManager()->flush();
        return $au;
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription()
    {
        return "Organize archival units by year.";
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return "AUsByYear";
    }

}
