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
class SecurityControllerTest extends FixturesWebTestCase
{

    public function testLoginLogoutAction()
    {
        /** @var Crawler */
        $crawler = $this->client->request('GET', '/login');

        /** @var Response */
        $response = $this->client->getResponse();
        
        /** @var Form */
        $form = $crawler->selectButton('Login')->form();
        $form['_username'] = 'user@example.com';
        $form['_password'] = 'supersecret';
        
        $crawler = $this->client->submit($form);
        $response = $this->client->getResponse();
        
        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
        $crawler = $this->client->followRedirect();
        $response = $this->client->getResponse();
        
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('a:contains("user@example.com")')->count());
        
        $crawler = $this->client->request('GET', '/logout');
        $response = $this->client->getResponse();
        
        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
        $crawler = $this->client->followRedirect();
        $response = $this->client->getResponse();
        $this->assertGreaterThan(0, $crawler->filter('a:contains("Login")')->count());
    }

}
