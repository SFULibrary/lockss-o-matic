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

namespace LOCKSSOMatic\CrudBundle\DataFixtures\ORM\dev;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CoreBundle\Utilities\AbstractDataFixture;
use LOCKSSOMatic\CrudBundle\Entity\Au;
use LOCKSSOMatic\CrudBundle\Entity\AuProperty;

/**
 * Load some test AU data into the database for testing.
 */
class LoadAuData extends AbstractDataFixture implements OrderedFixtureInterface
{

    /**
     * PLNs must be loaded before Boxes. This sets the order.
     *
     * @return int the order
     */
    public function getOrder()
    {
        return 5; // must be after content provider.
    }

    /**
     * Load the PLNs.
     *
     * @param ObjectManager $em
     */
    public function doload(ObjectManager $em)
    {
        $au = new Au();
        $au->setPln($this->getReference('pln-franklin'));
        $au->setContentprovider($this->getReference('provider'));
        $au->setPlugin($this->getReference('plugin'));
        $this->setReference('au', $au);

        $root = new AuProperty();
        $root->setPropertyKey('TestStuffGoesHere');
        $root->setAu($au);
        $this->setReference('auprop-root', $root);
        $em->persist($root);

        $this->buildAuProperty($em, 'param.1', 'base_url', 'http://example.com', $root, $au);
        $this->setReference('auprop-child', $this->buildAuProperty($em, 'param.2', 'pub_id', 'kittens', $root, $au));
        $this->buildAuProperty($em, 'param.3', 'foot_id', 'left', $root, $au);
        $this->buildAuProperty($em, 'param.4', 'year', '1991', $root, $au);


        $em->persist($au);
        $em->flush();
    }

    private function buildAuProperty(ObjectManager $em, $id, $key, $value, AuProperty $gp, Au $au) {
        $parent = new AuProperty();
        $parent->setAu($au);
        $parent->setParent($gp);
        $parent->setPropertyKey($id);
        $em->persist($parent);

        $keyProp = new AuProperty();
        $keyProp->setAu($au);
        $keyProp->setParent($parent);
        $keyProp->setPropertyKey('key');
        $keyProp->setPropertyValue($key);
        $em->persist($keyProp);
        $this->setReference('auprop-leaf', $keyProp);

        $valProp = new AuProperty();
        $valProp->setAu($au);
        $valProp->setParent($parent);
        $valProp->setPropertyKey('value');
        $valProp->setPropertyValue($value);
        $em->persist($valProp);

        return $parent;
    }

    /**
     * {@inheritDocs}
     */
    protected function getEnvironments()
    {
        return array('dev');
    }

}
