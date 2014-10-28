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
use LOCKSSOMatic\CRUDBundle\Entity\ContentProviders;

class LoadContentProvidersData extends AbstractFixture implements OrderedFixtureInterface
{
    
    public function getOrder()
    {
        return 2;
    }

    public function load(ObjectManager $manager)
    {
        $object = new ContentProviders();
        $object->setType('application');
        $object->setUuid('f705d95b-7a0a-451c-a51e-242bc4ab53cf');
        $object->setPermissionUrl('http://provider.example.com/path/to/permission/stmt');
        $object->setName('Example Provider Inc.');
        $object->setIpAddress('192.168.100.200');
        $object->setHostname('cp1.example.com');
        $object->setChecksumType('md5');
        // maxFileSize is in kB (1,000 bytes).
        $object->setMaxFileSize('123456');
        $object->setMaxAuSize('987654321');
        $object->setContentOwner($this->getReference('owner-1'));
        $object->setPln($this->getReference('pln-1'));
        
        $manager->persist($object);
        $manager->flush();
        $this->setReference('cp-1', $object);
    }
}
