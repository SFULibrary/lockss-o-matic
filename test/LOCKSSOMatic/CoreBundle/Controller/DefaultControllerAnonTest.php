<?php

namespace LOCKSSOMatic\CoreBundle\Controller;

use LOCKSSOMatic\CoreBundle\Utilities\AbstractTestCase;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerAnonTest extends AbstractTestCase
{
    protected $client;
    
    public function setUp() {
        parent::setUp();
        $this->client = static::createClient();
    }
    
    public function testIndex() {
        $this->client->request('GET', '/');
        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertEquals('http://localhost/login', $response->headers->get('Location'));
        $this->assertContains('Redirecting to', $response->getContent());
    }
    
    public function testMenus() {
        $this->client->request('GET', '/login');
        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        
        $crawler = $this->client->getCrawler()->filter('#navbar ul');
        $this->assertCount(2, $crawler);
        $main = $crawler->eq(0);
        $this->assertCount(1, $main->filter('li'));
        $this->assertEquals('Home', trim($main->filter('li')->eq(0)->text()));
        
        $user = $crawler->eq(1);
        $this->assertCount(1, $user->filter('li'));
        $this->assertEquals('Login', trim($user->filter('li')->eq(0)->text()));
    }
    
}
