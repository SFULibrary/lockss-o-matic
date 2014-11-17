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

namespace LOCKSSOMatic\SWORDBundle\Plugins\ausbytype;

use Bitworking\Mimeparse;
use LOCKSSOMatic\CRUDBundle\Entity\Aus;
use LOCKSSOMatic\CRUDBundle\Entity\ContentBuilder;
use LOCKSSOMatic\CRUDBundle\Entity\ContentProviders;
use LOCKSSOMatic\PluginBundle\Plugins\AbstractPlugin;
use LOCKSSOMatic\SWORDBundle\Event\DepositContentEvent;
use LOCKSSOMatic\SWORDBundle\Event\ServiceDocumentEvent;
use LOCKSSOMatic\SWORDBundle\Plugins\DepositPlugin;
use LOCKSSOMatic\SWORDBundle\Utilities\Namespaces;
use SimpleXMLElement;

/**
 * Organize AUs by type.
 */
class AusByType extends DepositPlugin
{

   public function requiredAttributes() {
        return array_merge(array('mimetype'), parent::requiredAttributes());
    }
    public function buildFilter($maxSize, $group, $contentSize) {
        $self = $this;
        return function(Aus $au) use($self, $maxSize, $group, $contentSize) {
            if ($au->getContentSize() + $contentSize >= $maxSize) {
                return false;
            }
            $data = $self->getData('AuParams', $au);
            if ($data === null) {
                return false;
            }
            if (array_key_exists('ByType', $data)
                && ($data['ByType'] === true)
                && ($data['group'] === $group)) {
                return true;
            }
            return false;
        };
    }

    /**
     * Called automatically when content is deposited. Finds or creates an
     * AU and adds the newly created content item to it.
     * 
     * @param DepositContentEvent $event
     */
    public function onDepositContent(DepositContentEvent $event)
    {
        if ($event->getPluginName() !== $this->getPluginId()) {
            return;
        }
        /** @var ContentProviders */
        $contentProvider = $event->getContentProvider();
        $deposit = $event->getDeposit();
        $contentXml = $event->getXml();

        $maxSize = $contentProvider->getMaxAuSize();
        $contentSize = (string) $contentXml->attributes()->size;
        $contentType = (string) $contentXml->attributes()->mimetype;
        
        $groupings = $this->getSetting('groupings');
        $groupKeys = array_keys($groupings);
        $group = null;

        for($i = 0; $i < count($groupKeys); $i++) {
            $group = $groupKeys[$i];
            $mimetype = Mimeparse::bestMatch($groupings[$group]['types'], $contentType);
            if($mimetype !== null) {
                break;
            }
        }
        
        $filter = $this->buildFilter($maxSize, $group, $contentSize);

        $this->container->get('doctrine')->getManager()->refresh($contentProvider);
        $aus = $contentProvider->getAus()->filter($filter);
        if ($aus->count() >= 1) {
            // of the aus returned, get the "best" match.
            $au = $aus->first();
        } else {
            $au = $this->buildAu(
                $contentProvider,
                'auid-type- ' . $group,
                'Created by AusByType for ' . $groupings[$group]['label'],
                'http://pln.example.com/foo/bar'
            );
            $this->setData('AuParams', $au,
                array('ByType' => true, 'group' => $group));
        }
        $this->depositContent($contentXml, $deposit, $au);
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription()
    {
        return "Organize archival units by Mimetype.";
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return "AUsByType";
    }

}
