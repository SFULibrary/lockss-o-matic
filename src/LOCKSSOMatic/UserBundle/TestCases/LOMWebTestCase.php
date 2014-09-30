<?php

/* 
 * The MIT License
 *
 * Copyright 2014 Michael Joyce <michael@negativespace.net>.
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

namespace LOCKSSOMatic\UserBundle\TestCases;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;

abstract class LOMWebTestCase extends WebTestCase {
    
    /**
     * @var Client
     */
    protected $client = null;

    protected $logger = null;
    
    public function __construct() {
        parent::__construct();
    }

    public function setUp()
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->client->restart();
        if($this->logger === null) {
            $this->logger = $this->client->getContainer()->get('logger');
        }
    }

    public function doLogin($username)
    {
        /** @var Crawler */        
        $crawler = $this->client->request('GET', '/login');

        /** @var Response */
        $response = $this->client->getResponse();
        
        /** @var Form */
        $form = $crawler->selectButton('Login')->form();
        $form['_username'] = $username;
        $form['_password'] = 'supersecret';
        
        $crawler = $this->client->submit($form);
    }
    
    public function doLogout() {
        $crawler = $this->client->request('GET', '/logout');
    }
    
}