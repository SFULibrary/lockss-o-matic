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

namespace LOCKSSOMatic\CrudBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CoreBundle\Utilities\AbstractDataFixture;
use LOCKSSOMatic\CrudBundle\Entity\Pln;

/**
 * Load some test data into the database.
 */
class LoadPlnData extends AbstractDataFixture implements OrderedFixtureInterface
{

    /**
     * PLNs must be loaded before Boxes. This sets the order.
     *
     * @return int the order
     */
    public function getOrder()
    {
        return 1;
    }

    /**
     * Load the PLNs.
     *
     * @param ObjectManager $manager
     */
    public function doLoad(ObjectManager $manager)
    {
        $franklin = $this->buildPln('franklin', $manager);
        $this->setReference('pln-franklin', $franklin);
        
        $dewey = $this->buildPln('dewey', $manager);
        $this->setReference('pln-dewey', $dewey);
        
        $larkin = $this->buildPln('larkin', $manager);
        $this->setReference('pln-larkin', $larkin);

        $jefferson = $this->buildPln('jefferson', $manager);
        $this->setReference('pln-jefferson', $jefferson);

        $borges = $this->buildPln('borges', $manager);
        $this->setReference('pln-borges', $borges);

        $manager->flush();
    }

    /**
     * Build and persist a PLN.
     *
     * @param string $name
     * @param ObjectManager $manager
     *
     * @return Pln
     */
    private function buildPln($name, ObjectManager $manager)
    {
        $pln = new Pln();
        $pln->setName($name);
        $pln->setPropserver("http://{$name}.example.com");
        $pln->setPropsPath("/{$name}/props");
        $manager->persist($pln);
        return $pln;
    }

    protected function getEnvironments()
    {
        return array('dev');
    }

}
