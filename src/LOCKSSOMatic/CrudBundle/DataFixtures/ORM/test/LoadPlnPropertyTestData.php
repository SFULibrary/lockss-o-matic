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

namespace LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CoreBundle\Utilities\AbstractDataFixture;
use LOCKSSOMatic\CrudBundle\Entity\Pln;
use LOCKSSOMatic\CrudBundle\Entity\PlnProperty;

/**
 * Load some test pln property data into the database.
 */
class LoadPlnPropertyTestData extends AbstractDataFixture implements OrderedFixtureInterface
{

    /**
     * PLNs must be loaded before Boxes. This sets the order.
     *
     * @return int the order
     */
    public function getOrder()
    {
        return 2;
    }

    /**
     * Load the PLNs.
     *
     * @param ObjectManager $em
     */
    public function doload(ObjectManager $em)
    {
        /** @var Pln $pln */
        $pln = $this->getReference('pln-franklin');
        $prop = $this->buildProperty($em, $pln, 'leaf', 'crunchyleaves');
        $season = $this->buildProperty($em, $pln, 'season', null);
        $this->buildProperty($em, $pln, 'autumn', 'lots of leaves', $season);
        $this->buildProperty($em, $pln, 'winter', 'not many leaves', $season);
        $this->buildProperty($em, $pln, 'list', array('foo', 'bar', 'baz'), $season);
        $em->flush();
    }

    /**
     * @param ObjectManager $em
     * @param type $key
     * @param type $value
     * @param PlnProperty $parent
     * @return PlnProperty
     */
    private function buildProperty(ObjectManager $em, Pln $pln, $key, $value, PlnProperty $parent = null) {
        $prop = new PlnProperty();
        $prop->setPln($pln);
        $prop->setPropertyKey($key);
        $prop->setPropertyValue($value);
        $prop->setParent($parent);
        $this->setReference("plnprop-{$key}", $prop);
        $em->persist($prop);
        return $prop;
    }

    /**
     * {@inheritDocs}
     */
    protected function getEnvironments()
    {
        return array('test');
    }

}
