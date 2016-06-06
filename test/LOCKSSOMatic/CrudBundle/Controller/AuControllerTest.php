<?php

namespace LOCKSSOMatic\CrudBundle\Controller;

use LOCKSSOMatic\CoreBundle\Utilities\AbstractTestCase;

class AuControllerTest extends AbstractTestCase {

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
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadContentOwner',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadContentProvider',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadDeposit',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadAu',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadContent',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadContentProperty',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPlugin',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPluginProperty',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPln',
            'LOCKSSOMatic\UserBundle\DataFixtures\ORM\test\LoadUser',
        );
    }
    
    public function testIndex() {
        $this->client->request('GET', '/pln/1/au/');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        
        $crawler = $this->client->getCrawler();
        $links = $crawler->filter('table.table a');
        $this->assertCount(1, $links);
        $this->assertEquals('/pln/1/au/1', $links->first()->attr('href'));
    }    
    
    public function testIndexBadPln() {
        $this->client->request('GET', '/pln/3/au/');
        $response = $this->client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
    }  
    
    public function testShow() {
        $this->client->request('GET', '/pln/1/au/1');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        
        $link = $this->client->getCrawler()->selectLink('Not checked yet');
        $this->assertNotNull($link);
        $this->assertEquals('/pln/1/au/1/status', $link->attr('href'));
    }    
    
    public function testShowBadPln() {
        $this->client->request('GET', '/pln/3/au/1');
        $response = $this->client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
    }    
    
    public function testShowBadAu() {
        $this->client->request('GET', '/pln/1/au/3');
        $response = $this->client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
    }    
    
    public function testAuStatus() {
        $this->client->request('GET', '/pln/1/au/1/status');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());        
    }
}
