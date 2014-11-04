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

namespace LOCKSSOMatic\UserBundle\Tests\Controller;

use Doctrine\ORM\EntityManager;
use LOCKSSOMatic\UserBundle\Entity\User;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test that the registration controller is properly overridden and 
 * requires an admin user for all actions.
 */
class SecurityControllerTest extends WebTestCase
{
    /**
     * @var EntityManager
     */
    private static $em;
    
    /**
     * @var Logger 
     */
    private $logger;
    
    /**
     * @var ContainerInterface
     */
    private $container;
    
    public function __construct()
    {
        parent::__construct();        
        static::bootKernel();
        $this->container = static::$kernel->getContainer();
        $this->logger = $this->container->get('logger');
        self::$em = $this->container->get('doctrine')->getManager();
    }
    
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        $user = new User();
        $user->setUsername('sectest@example.com');
        $user->setEmail('sectest@example.com');
        $user->setFullname('Test User');
        $user->setInstitution('Test Institution');
        $user->setEnabled(true);
        $user->setPlainPassword('supersecret');
        self::$em->persist($user);
        self::$em->flush();
    }
    
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        $user = self::$em->getRepository('LOCKSSOMaticUserBundle:User')->findOneBy(array('email' => 'sectest@example.com'));
        self::$em->remove($user);
        self::$em->flush();
    }

    public function testLoginLogoutAction()
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/login');

        /** @var Form */
        $form = $crawler->selectButton('Login')->form();
        $form['_username'] = 'sectest@example.com';
        $form['_password'] = 'supersecret';
        
        $crawler = $client->submit($form);        
        $response = $client->getResponse();
        
        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
        $crawler = $client->followRedirect();
        $response = $client->getResponse();
        
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('a:contains("sectest@example.com")')->count());
        
        $crawler = $client->request('GET', '/logout');
        $response = $client->getResponse();
        
        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
        $crawler = $client->followRedirect();
        $crawler = $client->followRedirect(); // two redirects here, because security.
        $response = $client->getResponse();

        $this->assertGreaterThan(0, $crawler->filter('a:contains("Login")')->count());
    }

}
