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

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityManager;
use LOCKSSOMatic\UserBundle\Entity\User;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Response;


class AdminUserControllerTest extends WebTestCase
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
        $user->setUsername('user@example.com');
        $user->setEmail('user@example.com');
        $user->setFullname('Test User');
        $user->setInstitution('Test Institution');
        $user->setEnabled(true);
        $user->setPlainPassword('supersecret');
        self::$em->persist($user);
        
        $admin = new User();
        $admin->setUsername('admin@example.com');
        $admin->setEmail('admin@example.com');
        $admin->setFullname('Test User');
        $admin->setInstitution('Test Institution');
        $admin->setEnabled(true);
        $admin->setPlainPassword('supersecret');
        $admin->addRole('ROLE_ADMIN');
        self::$em->persist($admin);
        
        self::$em->flush();
    }
    
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        $user = self::$em->getRepository('LOCKSSOMaticUserBundle:User')->findOneBy(array(
            'email' => 'user@example.com'
        ));
        self::$em->remove($user);
        $admin = self::$em->getRepository('LOCKSSOMaticUserBundle:User')->findOneBy(array(
            'email' => 'admin@example.com'
        ));
        self::$em->remove($admin);
        self::$em->flush();
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
        $response = $client->getResponse();
        if($response->getStatusCode() !== 200) {
            $this->logger->error($response->getContent());
        }
        return $client;
    }
    
    public function testIndexAction() {
        
        $client = $this->doLogin('admin@example.com', 'supersecret');
        
        /** @var Crawler */
        $crawler = $client->request('GET', '/admin/user/');
        
        /** @var Response */
        $response = $client->getResponse();
        
        $this->assertGreaterThan(0, $crawler->filter('a:contains("show")')->count());
    }
    
    public function testNewAction() {
        
        $client = $this->doLogin('admin@example.com', 'supersecret');
        
        /** @var Crawler */
        $crawler = $client->request('GET', '/admin/user/new');
        
        /** @var Response */
        $response = $client->getResponse();
        
        /** @var Form */
        $form = $crawler->selectButton("Create")->form();
        $form['lom_userbundle_user[email]'] = 'optimus@example.com';
        $form['lom_userbundle_user[fullname]'] = 'Optimus Prime';
        $form['lom_userbundle_user[institution]'] = 'Autobots';
        $form['lom_userbundle_user[roles]'][1]->tick();
        $form['lom_userbundle_user[enabled]'] = 1;
        $client->submit($form);
        
        $crawler = $client->followRedirect();
        $this->assertGreaterThan(0, $crawler->filter('td:contains("optimus@example.com")')->count());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Optimus Prime")')->count());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Autobots")')->count());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("ROLE_LOMADMIN")')->count());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("optimus@example.com")')->count());
        
        /** @var User */
        $user = self::$em->getRepository('LOCKSSOMaticUserBundle:User')->findOneBy(array('username' => 'optimus@example.com'));
        $this->assertNotNull($user);
        $this->assertEquals('optimus@example.com', $user->getEmail());
        $this->assertEquals('optimus@example.com', $user->getUsername());
    }
    
    public function testShowAction() {
        $client = $this->doLogin('admin@example.com', 'supersecret');
        
        /** @var User */
        $user = self::$em->getRepository('LOCKSSOMaticUserBundle:User')->findOneBy(array('username' => 'optimus@example.com'));
        
        $this->assertNotNull($user);
        
        /** @var Crawler */
        $crawler = $client->request('GET', '/admin/user/' . $user->getId() . '/show');
        
        /** @var Response */
        $response = $client->getResponse();
        
        $this->assertGreaterThan(0, $crawler->filter('td:contains("optimus@example.com")')->count());        
    }
        
    public function testEditAction() {
        $client = $this->doLogin('admin@example.com', 'supersecret');
        
        /** @var User */
        $user = self::$em->getRepository('LOCKSSOMaticUserBundle:User')->findOneBy(array('username' => 'optimus@example.com'));
        $this->assertNotNull($user);

        /** @var Crawler */
        $crawler = $client->request('GET', '/admin/user/' . $user->getId() . '/edit');
        
        /** @var Response */
        $response = $client->getResponse();
        
        /** @var Form */
        $form = $crawler->selectButton("Update")->form();
        $form['lom_userbundle_user[email]'] = 'optimusprime@example.com';
        $form['lom_userbundle_user[fullname]'] = 'Optimus Prime';
        $form['lom_userbundle_user[institution]'] = 'Autobots';
        $form['lom_userbundle_user[roles]'][1]->tick();
        $form['lom_userbundle_user[enabled]'] = 1;
        $client->submit($form);
        
        $crawler = $client->request('GET', '/admin/user/' . $user->getId() . '/show');
        $this->assertGreaterThan(0, $crawler->filter('td:contains("optimusprime@example.com")')->count());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Optimus Prime")')->count());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Autobots")')->count());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("ROLE_LOMADMIN")')->count());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("optimusprime@example.com")')->count());
        
        self::$em->refresh($user);
        $this->assertNotNull($user);
        $this->assertEquals('optimusprime@example.com', $user->getEmail());
        $this->assertEquals('optimusprime@example.com', $user->getUsername());
        
        self::$em->remove($user);
        self::$em->flush();
    }
    
}
