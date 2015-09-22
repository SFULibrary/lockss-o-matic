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
use LOCKSSOMatic\CrudBundle\Entity\Box;
use LOCKSSOMatic\CrudBundle\Entity\Pln;

/**
 * Load some test data.
 */
class LoadBoxData extends AbstractDataFixture implements OrderedFixtureInterface
{

    /**
     * Boxes must be loaded after PLNs.
     *
     * @return int the order
     */
    public function getOrder()
    {
        return 2;
    }
    
    /**
     * Load the boxes
     *
     * @param ObjectManager $manager
     */
    public function doLoad(ObjectManager $manager)
    {
        $this->buildBox('aaron', 'pln-borges', $manager);
        $this->buildBox('adrian', 'pln-dewey', $manager);
        $this->buildBox('alice', 'pln-franklin', $manager);
        $this->buildBox('titus', 'pln-jefferson', $manager);
        $this->buildBox('puck', 'pln-larkin', $manager);
        $this->buildBox('ethel', 'pln-borges', $manager);
        $this->buildBox('barnardo', 'pln-dewey', $manager);
        $this->buildBox('bassianus', 'pln-franklin', $manager);
        $this->buildBox('banquo', 'pln-jefferson', $manager);
        $this->buildBox('duncan', 'pln-larkin', $manager);
        $this->buildBox('benedick', 'pln-borges', $manager);
        $this->buildBox('cassio', 'pln-dewey', $manager);
        $this->buildBox('henry', 'pln-franklin', $manager);
        $this->buildBox('arthur', 'pln-jefferson', $manager);
        $this->buildBox('brabantio', 'pln-larkin', $manager);
        $this->buildBox('brutus', 'pln-borges', $manager);
        $this->buildBox('burgundy', 'pln-dewey', $manager);
        $this->buildBox('caliban', 'pln-franklin', $manager);
        $this->buildBox('ceres', 'pln-jefferson', $manager);
        $this->buildBox('orlando', 'pln-larkin', $manager);
        $this->buildBox('cladius', 'pln-borges', $manager);
        $this->buildBox('cressida', 'pln-dewey', $manager);
        $this->buildBox('troilus', 'pln-franklin', $manager);
        $this->buildBox('cymbeline', 'pln-jefferson', $manager);
        $this->buildBox('demetrius', 'pln-larkin', $manager);
        $this->buildBox('dogberry', 'pln-borges', $manager);
        $this->buildBox('edward', 'pln-dewey', $manager);
        $this->buildBox('elenor', 'pln-franklin', $manager);
        $this->buildBox('fleance', 'pln-jefferson', $manager);
        $manager->flush();
    }
    
    /**
     * Build and persist a box.
     *
     * @param string $hostname
     * @param Pln $network
     * @param ObjectManager $manager
     *
     * @return Box
     */
    private function buildBox($hostname, $network, ObjectManager $manager)
    {
        $box = new Box();
        $box->setHostName($hostname);
        $box->setIpAddress("127.0.0.1");
        $box->setUsername("{$hostname}_user");
        $box->setPassword("supersecret");
        $box->setPln($this->getReference($network));
        $manager->persist($box);
        return $box;
    }
    
    protected function getEnvironments()
    {
        return array('dev');
    }
}
