<?php

/* 
 * The MIT License
 *
 * Copyright (c) 2014 Mark Jordan, mjordan@sfu.ca.
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

namespace LOCKSSOMatic\CRUDBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CRUDBundle\Entity\Content;

class LoadContentData extends AbstractFixture implements OrderedFixtureInterface
{
    
    public function getOrder()
    {
        return 4; // after deposits and aus
    }

    public function load(ObjectManager $manager)
    {
        $object = new Content();
        $object->setUrl('http://example.com/path/item/1');
        $object->setTitle('Some example title');
        $object->setSize('41445');
        $object->setChecksumType('md5');
        $object->setChecksumValue('eb4585ad9fe0426781ed7c49252f8225');
        $object->setRecrawl(1);
        $object->setDeposit($this->getReference('deposit-1'));
        $object->setAu($this->getReference('au-1'));
        $object->setVerifiedSize(false);
        $manager->persist($object);
        $manager->flush();
        $this->setReference('cp-1', $object);
    }
}
