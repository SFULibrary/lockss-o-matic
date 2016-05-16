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
    
}
