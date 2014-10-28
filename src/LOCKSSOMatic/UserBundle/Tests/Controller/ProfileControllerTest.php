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
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Tests\Logger;

/**
 * Test that the registration controller is properly overridden and 
 * requires an admin user for all actions.
 */
class ProfileControllerTest extends WebTestCase
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
        $user->setUsername('profiletest@example.com');
        $user->setEmail('profiletest@example.com');
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
        $user = self::$em->getRepository('LOCKSSOMaticUserBundle:User')->findOneBy(array(
            'email' => 'anonyx@example.com' // gets changed during the test.
        ));
        self::$em->remove($user);
        self::$em->flush();
    }

    public function testAnonProfileAccess()
    {
        $client = self::createClient();
        $client->request('GET', '/profile/');

        /** @var Response */
        $response = $client->getResponse();
        
        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertStringEndsWith('/login', $response->headers->get('location'));
    }

    public function doLogin($username, $password)
    {
        $client = self::createClient();
        $client->restart();
        
        $crawler = $client->request('GET', '/login');
        $response = $client->getResponse();
        $form = $crawler->selectButton('Login')->form();
        $form['_username'] = $username;
        $form['_password'] = $password;
        
        $crawler = $client->submit($form);
        return $client;
    }

    public function testProfileEditAction() {
        $client = $this->doLogin('profiletest@example.com', 'supersecret');
        
        $crawler = $client->request('GET', '/profile/');
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('h1:contains("Profile")')->count());

        $crawler = $client->request('GET', '/profile/edit');
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $form = $crawler->selectButton('Update')->form();
        $form['fos_user_profile_form[email]'] = 'anonyx@example.com';
        $form['fos_user_profile_form[fullname]'] = 'Anony Xavier';
        $form['fos_user_profile_form[institution]'] = 'A school for the not very gifted';
        $form['fos_user_profile_form[current_password]'] = 'supersecret';        
        
        $crawler = $client->submit($form);
        $this->logger->error($crawler->html());
        $crawler = $client->followRedirect();
        
        $this->assertGreaterThan(0, $crawler->filter('td:contains("anonyx@example.com")')->count());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Anony Xavier")')->count());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("A school for the not very gifted")')->count());
    }
    
}
