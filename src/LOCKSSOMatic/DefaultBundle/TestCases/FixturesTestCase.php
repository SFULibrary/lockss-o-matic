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

namespace LOCKSSOMatic\DefaultBundle\TestCases;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * Extend the WebTestCase class, and add some fixtures.
 */
abstract class FixturesTestCase extends \PHPUnit_Framework_TestCase
{

    /**
     * @var AppKernel
     */
    protected static $kernel;

    /**
     * @var ContainerInterface
     */
    protected static $container;

    /**
     * The application, so that console commands can be run
     * inside the test setup.
     *
     * @var Application $application
     */
    protected static $application;

    /**
     * Database access
     *
     * @var Registry $doctrine
     */
    protected static $doctrine;

    /**
     * Construct a test case.
     */
    public function __construct()
    {
        parent::__construct();
        self::$kernel = new \AppKernel('test', true);
        self::$kernel->boot();
        self::$application = new Application(self::$kernel);
        self::$application->setAutoExit(false);
        self::$container = self::$kernel->getContainer();
        self::$doctrine = $this->get('doctrine');
    }

    protected function get($containerId)
    {
        return self::$container->get($containerId);
    }

    /**
     * Do the setup - drop and recreate the schema and load the
     * test data.
     */
    protected function setUp()
    {
        self::runCommand('doctrine:schema:drop --force');
        self::runCommand('doctrine:schema:create');
        self::runCommand('doctrine:fixtures:load -n');
    }

    /**
     * Run a command.
     *
     * @param string $command
     *
     * @return int 0 if everything went fine, or an error code
     */
    protected static function runCommand($command)
    {
        $command = sprintf('%s --quiet', $command);

        return self::$application->run(new StringInput($command));
    }
}
