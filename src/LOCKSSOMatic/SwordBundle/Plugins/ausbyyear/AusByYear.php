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

namespace LOCKSSOMatic\SwordBundle\Plugins\ausbyyear;

use Doctrine\Common\Util\Debug;
use LOCKSSOMatic\CrudBundle\Entity\Au;
use LOCKSSOMatic\CrudBundle\Entity\ContentProvider;
use LOCKSSOMatic\SwordBundle\Event\DepositContentEvent;
use LOCKSSOMatic\SwordBundle\Plugins\DepositPlugin;

/**
 * Organize AUs by year published, while respecting the content provider's maximum
 * AU size.
 */
class AusByYear extends DepositPlugin
{
    public function requiredAttributes() {
        return array_merge(array('year'), parent::requiredAttributes());
    }

    public function buildFilter($maxSize, $contentSize, $contentYear) {
        $self = $this;
        return function(Au $au) use ($self, $maxSize, $contentSize, $contentYear) {
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
    }

    /**
     * Called automatically when new content is deposited. Finds or creates an
     * Au based on the year and size of the content.
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
        $contentSize = (string) $contentXml->attributes()->size;
        $contentYear = (string) $contentXml->attributes()->year;

        $filter = $this->buildFilter($maxSize, $contentSize, $contentYear);

        $this->container->get('doctrine')->getManager()->refresh($contentProvider);
        $aus = $contentProvider->getAus()->filter($filter);
        if ($aus->count() >= 1) {
            $au = $aus->first();
        } else {
            $au = $this->buildAu(
                $contentProvider,
                'Created by AusByYear for ' . $contentYear,
                'http://pln.example.com/foo/year'
            );
            $this->setData('AuParams', $au, array('ByYear' => true, 'year' => $contentYear));
        }
        $this->depositContent($contentXml, $deposit, $au);
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
