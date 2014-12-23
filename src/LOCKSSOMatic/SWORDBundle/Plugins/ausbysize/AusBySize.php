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

namespace LOCKSSOMatic\SWORDBundle\Plugins\ausbysize;

use LOCKSSOMatic\CRUDBundle\Entity\Aus;
use LOCKSSOMatic\SWORDBundle\Event\DepositContentEvent;
use LOCKSSOMatic\SWORDBundle\Plugins\DepositPlugin;

/**
 * Organize AUs by size.
 */
class AusBySize extends DepositPlugin
{

    public function buildFilter($maxSize, $contentSize) {
        $self = $this;
        return function(Aus $au) use($self, $maxSize, $contentSize) {
            if ($au->getContentSize() + $contentSize >= $maxSize) {
                return false;
            }
            $data = $self->getData('AuParams', $au);
            if ($data === null) {
                return false;
            }
            return true;
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
        if(parent::onDepositContent($event) === false) {
            return;
        }

        $contentProvider = $event->getContentProvider();
        $deposit = $event->getDeposit();
        $contentXml = $event->getXml();

        $maxSize = $contentProvider->getMaxAuSize();
        $contentSize = (string)$contentXml->attributes()->size;

        $filter = $this->buildFilter($maxSize, $contentSize);

        $this->container->get('doctrine')->getManager()->refresh($contentProvider);
        $aus = $contentProvider->getAus()->filter($filter);
        if ($aus->count() >= 1) {
            $au = $aus->first();
        } else {
            $au = $this->buildAu(
                $contentProvider,
                'auid-size',
                'Created by AusBySize',
                'http://pln.example.com/foo/size'
            );
            $this->setData('AuParams', $au, array('BySize' => true));
        }
        $this->depositContent($contentXml, $deposit, $au);
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
