<?php

namespace LOCKSSOMatic\CrudBundle\Controller;

use LOCKSSOMatic\CoreBundle\Utilities\AbstractTestCase;

class ContentControllerTest extends AbstractTestCase {

    protected $client;

    public function setUp() {
        parent::setUp();
        $this->client = static::createClient(array(), array(
                'PHP_AUTH_USER' => 'admin@example.com',
                'PHP_AUTH_PW' => 'supersecret',
        ));
    }

    public function fixtures() {
        return array(
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadAu',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadAuProperty',            
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadContentOwner',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadDeposit',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadContent',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadContentProperty',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadContentProvider',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPlugin',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPluginProperty',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPln',
            'LOCKSSOMatic\UserBundle\DataFixtures\ORM\test\LoadUser',
        );
    }
   
    public function testIndex() {
        $this->client->request('GET', '/pln/1/content/');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        
        $crawler = $this->client->getCrawler();
        $this->assertCount(3, $crawler->filter('table.table a'));
    }    
    
    public function testIndexBadPln() {
        $this->client->request('GET', '/pln/3/content/');
        $response = $this->client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
    }    
    
    public function testShow() {
        $this->client->request('GET', '/pln/1/content/1');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());        
    }
    
    public function testShowBadPln() {
        $this->client->request('GET', '/pln/3/content/1');
        $response = $this->client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());        
    }

    public function testShowBadBox() {
        $this->client->request('GET', '/pln/1/content/97');
        $response = $this->client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());        
    }

}
