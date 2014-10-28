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
use LOCKSSOMatic\CRUDBundle\Entity\AuProperties;

class LoadAuPropertyData extends AbstractFixture implements OrderedFixtureInterface
{
    
    public function getOrder()
    {
        return 3;
    }

    public function load(ObjectManager $manager)
    {
        $object = new AuProperties();
        $object->setPropertyKey('test');
        $object->setPropertyValue('yes');
        $object->setAu($this->getReference('au-1'));
        $manager->persist($object);
        $manager->flush();
        $this->setReference('auprop-1', $object);
        
        $object = new AuProperties();
        $object->setPropertyKey('example');
        $object->setPropertyValue('no');
        $object->setAu($this->getReference('au-1'));
        $object->setParent($this->getReference('auprop-1'));
        $manager->persist($object);
        $manager->flush();

        $object = new AuProperties();
        $object->setPropertyKey('recursive');
        $object->setPropertyValue('P. 269: recursion  86, 139, 141, 182, 202, 269');
        $object->setAu($this->getReference('au-1'));
        $object->setParent($this->getReference('auprop-1'));
        $manager->persist($object);
        $manager->flush();
    }
}
