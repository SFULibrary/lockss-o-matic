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
use Symfony\Component\HttpFoundation\Response;

/**
 * Test that the registration controller is properly overridden and 
 * requires an admin user for all actions.
 */
class RegistrationControllerTest extends WebTestCase
{

    /**
     * @var Client
     */
    private $client = null;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->client->restart();
    }

    public function testRegisterActions()
    {
        $urls = array(
            '/register',
            '/register/check-email',
            '/register/confirm/17368237462',
            '/register/confirmed'
        );

        foreach ($urls as $url) {
            /** @var Crawler */
            $crawler = $this->client->request('GET', '/register');
            
            /** @var Response */
            $response = $this->client->getResponse();

            // $response->getStatusCode() will be MOVED_PERMANENTLY (301) 
            // if the register line is removed from security.yaml.
            $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
            $this->assertStringEndsWith('/login', $response->headers->get('location'));
            $this->assertGreaterThan(0, $crawler->filter('html:contains("Redirecting")')->count());
        }
    }

}
