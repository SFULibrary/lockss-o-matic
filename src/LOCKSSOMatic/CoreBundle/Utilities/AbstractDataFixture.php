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

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * This class is a wrapper around AbstractFixture. When loading data it checks
 * which environment is active, and then only loads fixtures for that
 * environment.
 *
 * http://stackoverflow.com/questions/11817971
 */
abstract class AbstractDataFixture extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{

    protected $container;

    /**
     * Check if the class should load data, and then load it via the overridden
     * doLoad() method.
     */
    final public function load(ObjectManager $em)
    {
        /** @var KernelInterface $kernel */
        $kernel = $this->container->get('kernel');
        if (in_array($kernel->getEnvironment(), $this->getEnvironments())) {
            $this->doLoad($em);
        } else {
            $this->container->get('logger')->notice('skipped.');
        }
    }

    /**
     * {@inheritDocs}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDocs}
     */
    public function getOrder()
    {
        return 1;
    }

    /**
     * Load the data into the database.
     * @param ObjectManager $manager
     */
    abstract protected function doLoad(ObjectManager $manager);

    /**
     * Fixtures use this function to return an array listing the environments
     * that they should be activated for.
     */
    abstract protected function getEnvironments();
}
