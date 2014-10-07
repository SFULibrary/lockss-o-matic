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

use LOCKSSOMatic\UserBundle\Entity\User;
use LOCKSSOMatic\DefaultBundle\TestCases\FixturesWebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Response;

use Doctrine\Common\Util\Debug; 

class AdminUserControllerTest extends FixturesWebTestCase
{

    public function setUp() {
        parent::setUp();
        $this->doLogin('admin@example.com');
    }
    
    public function testIndexAction() {
        
        /** @var Crawler */
        $crawler = $this->client->request('GET', '/admin/user/');
        
        /** @var Response */
        $response = $this->client->getResponse();
        
        $this->assertGreaterThan(0, $crawler->filter('a:contains("show")')->count());
    }
    
    public function testNewAction() {
        /** @var Crawler */
        $crawler = $this->client->request('GET', '/admin/user/new');
        
        /** @var Response */
        $response = $this->client->getResponse();
        
        /** @var Form */
        $form = $crawler->selectButton("Create")->form();
        $form['lom_userbundle_user[email]'] = 'optimus@example.com';
        $form['lom_userbundle_user[fullname]'] = 'Optimus Prime';
        $form['lom_userbundle_user[institution]'] = 'Autobots';
        $form['lom_userbundle_user[roles]'][1]->tick();
        $form['lom_userbundle_user[enabled]'] = 1;
        $this->client->submit($form);
        
        $crawler = $this->client->followRedirect();
        $this->assertGreaterThan(0, $crawler->filter('td:contains("optimus@example.com")')->count());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Optimus Prime")')->count());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Autobots")')->count());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("ROLE_LOMADMIN")')->count());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("optimus@example.com")')->count());
        
        $em = $this->get('doctrine')->getManager();
        
        /** @var User */
        $user = $em->getRepository('LOCKSSOMaticUserBundle:User')->findOneBy(array('username' => 'optimus@example.com'));
        $this->assertNotNull($user);
        $this->assertEquals('optimus@example.com', $user->getEmail());
        $this->assertEquals('optimus@example.com', $user->getUsername());
    }
    
    public function testShowAction() {
        $em = $this->get('doctrine')->getManager();
        
        /** @var User */
        $user = $em->getRepository('LOCKSSOMaticUserBundle:User')->findOneBy(array('username' => 'user@example.com'));
        
        /** @var Crawler */
        $crawler = $this->client->request('GET', '/admin/user/' . $user->getId() . '/show');
        
        /** @var Response */
        $response = $this->client->getResponse();
        
        $this->assertGreaterThan(0, $crawler->filter('td:contains("user@example.com")')->count());        
    }
        
    public function testEditAction() {
        $em = $this->get('doctrine')->getManager();
        /** @var User */
        $user = $em->getRepository('LOCKSSOMaticUserBundle:User')->findOneBy(array('username' => 'user@example.com'));
        $this->assertNotNull($user);

        /** @var Crawler */
        $crawler = $this->client->request('GET', '/admin/user/' . $user->getId() . '/edit');
        
        /** @var Response */
        $response = $this->client->getResponse();
        
        /** @var Form */
        $form = $crawler->selectButton("Update")->form();
        $form['lom_userbundle_user[email]'] = 'optimus@example.com';
        $form['lom_userbundle_user[fullname]'] = 'Optimus Prime';
        $form['lom_userbundle_user[institution]'] = 'Autobots';
        $form['lom_userbundle_user[roles]'][1]->tick();
        $form['lom_userbundle_user[enabled]'] = 1;
        $this->client->submit($form);
        
        $crawler = $this->client->request('GET', '/admin/user/' . $user->getId() . '/show');
        $this->assertGreaterThan(0, $crawler->filter('td:contains("optimus@example.com")')->count());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Optimus Prime")')->count());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Autobots")')->count());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("ROLE_LOMADMIN")')->count());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("optimus@example.com")')->count());
        
        $em->refresh($user);
        $this->assertNotNull($user);
        $this->assertEquals('optimus@example.com', $user->getEmail());
        $this->assertEquals('optimus@example.com', $user->getUsername());
    }
    
}
