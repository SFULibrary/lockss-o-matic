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

namespace LOCKSSOMatic\CoreBundle\Utilities;

use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Liip\FunctionalTestBundle\Test\WebTestCase as BaseTestCase;

/**
 * Thin wrapper around Liip\FunctionalTestBundle\Test\WebTestCase to preload
 * fixtures into the database.
 */
class AbstractTestCase extends BaseTestCase {

    /**
     * @var ObjectManager
     */
    protected $em;

    /**
     * As the fixtures load data, they save references. Use $this->references
     * to get them.
     * 
     * @var ReferenceRepository
     */
    protected $references;

    /**
     * {@inheritDocs}
     */
    protected function setUp() {
        $fixtures = array(
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPlnTestData',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPlnPropertyTestData',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPluginTestData',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPluginPropertyTestData',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadOwnerTestData',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadProviderTestData',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadDepositTestData',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadAuTestData',
        );
        $this->references = $this->loadFixtures($fixtures)->getReferenceRepository();
        $this->em = $this->getContainer()->get('doctrine')->getManager();
    }
}