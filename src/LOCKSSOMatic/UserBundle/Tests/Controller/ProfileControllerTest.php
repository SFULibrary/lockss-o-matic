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

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Response;

use LOCKSSOMatic\UserBundle\TestCases\FixturesWebTestCase;

/**
 * Test that the registration controller is properly overridden and 
 * requires an admin user for all actions.
 */
class ProfileControllerTest extends FixturesWebTestCase
{

    public function testAnonProfileAccess()
    {
        /** @var Crawler */
        $crawler = $this->client->request('GET', '/profile/');

        /** @var Response */
        $response = $this->client->getResponse();
        
        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertStringEndsWith('/login', $response->headers->get('location'));
    }

    public function testProfileEditAction() {
        $this->doLogin('user@example.com');
        
        /** @var Crawler */
        $crawler = $this->client->request('GET', '/profile/');

        /** @var Response */
        $response = $this->client->getResponse();
        $this->assertGreaterThan(0, $crawler->filter('h1:contains("Profile")')->count());

        $crawler = $this->client->request('GET', '/profile/edit');
        /** @var Form */
        $form = $crawler->selectButton('Update')->form();
        $form['fos_user_profile_form[email]'] = 'anonyx@example.com';
        $form['fos_user_profile_form[fullname]'] = 'Anony Xavier';
        $form['fos_user_profile_form[institution]'] = 'A school for the not very gifted';
        $form['fos_user_profile_form[current_password]'] = 'supersecret';        
        
        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        
        $this->assertGreaterThan(0, $crawler->filter('td:contains("anonyx@example.com")')->count());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Anony Xavier")')->count());
        $this->assertGreaterThan(0, $crawler->filter('td:contains("A school for the not very gifted")')->count());
        
        // make sure the username and email were both updated.
        $em = $this->get('doctrine')->getManager();
        $user = $em->getRepository('LOCKSSOMaticUserBundle:User')->findOneBy(array('username' => 'anonyx@example.com'));
        $em->refresh($user); // refresh the user - may be cached from a previous test.
        $this->assertNotNull($user);
        $this->assertEquals('anonyx@example.com', $user->getEmail());
        $this->assertEquals('anonyx@example.com', $user->getUsername());
    }
    
}
