<?php

namespace LOCKSSOMatic\CrudBundle\Controller;

use LOCKSSOMatic\CoreBundle\Utilities\AbstractTestCase;

class BoxControllerTest extends AbstractTestCase {

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
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadBox',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadBoxStatus',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadCacheStatus',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPln',
            'LOCKSSOMatic\UserBundle\DataFixtures\ORM\test\LoadUser',
        );
    }
   
    public function testIndex() {
        $this->client->request('GET', '/pln/1/box/');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        
        $crawler = $this->client->getCrawler();
        $this->assertCount(2, $crawler->selectLink('localhost'));
    }    
    
    public function testIndexBadPln() {
        $this->client->request('GET', '/pln/3/box/');
        $response = $this->client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
    }    
    
    public function testShow() {
        $this->client->request('GET', '/pln/1/box/1');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        
        $crawler = $this->client->getCrawler();
        $this->assertCount(1, $crawler->selectLink('localhost'));
        $this->assertEquals('http://localhost:8089', $crawler->selectLink('localhost')->first()->attr('href'));
        
        $this->assertCount(1, $crawler->selectLink('Details'));
        $this->assertEquals('/pln/1/box/1/status', $crawler->selectLink('Details')->first()->attr('href'));        
    }
    
    public function testShowBadPln() {
        $this->client->request('GET', '/pln/3/box/1');
        $response = $this->client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());        
    }

    public function testShowBadBox() {
        $this->client->request('GET', '/pln/1/box/3');
        $response = $this->client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());        
    }

    public function testStatus() {
        $this->client->request('GET', '/pln/1/box/1/status');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
    }
    
    public function testStatusBadPln() {
        $this->client->request('GET', '/pln/3/box/1/status');
        $response = $this->client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
    }
    
    public function testStatusBadBox() {
        $this->client->request('GET', '/pln/1/box/4/status');
        $response = $this->client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
    }
    
    public function testCreate() {
        $this->client->request('GET', '/pln/1/box/new');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        
        $crawler = $this->client->getCrawler();
        $formCrawler = $crawler->selectButton('Create');
        $form = $formCrawler->form(array(
            'lockssomatic_crudbundle_box[hostname]' => 'localhost',
            'lockssomatic_crudbundle_box[protocol]' => 'TCP',
            'lockssomatic_crudbundle_box[ipAddress]' => '',
            'lockssomatic_crudbundle_box[port]' => '1234',
            'lockssomatic_crudbundle_box[webServicePort]' => '12345',
        ));
        $this->client->submit($form);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/pln/1/box/3', $this->client->getResponse()->headers->get('Location'));
        
        $this->client->request('GET', '/pln/1/box/3');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('127.0.0.1', $this->client->getResponse()->getContent());
        
        $box = $this->em->find('LOCKSSOMaticCrudBundle:Box', 3);
        $this->assertInstanceOf('LOCKSSOMatic\CrudBundle\Entity\Box', $box);
        $this->assertEquals('localhost', $box->getHostname());
        $this->assertEquals('TCP', $box->getProtocol());
        $this->assertEquals('127.0.0.1', $box->getIpAddress());
        $this->assertEquals(1234, $box->getPort());
        $this->assertEquals(12345, $box->getWebServicePort());
    }

    public function testEdit() {
        $this->client->request('GET', '/pln/1/box/1/edit');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        
        $crawler = $this->client->getCrawler();
        $formCrawler = $crawler->selectButton('Update');
        $form = $formCrawler->form(array(
            'lockssomatic_crudbundle_box[hostname]' => 'localhost',
            'lockssomatic_crudbundle_box[protocol]' => 'TCP',
            'lockssomatic_crudbundle_box[ipAddress]' => '',
            'lockssomatic_crudbundle_box[port]' => '1234',
            'lockssomatic_crudbundle_box[webServicePort]' => '12345',
        ));
        $this->client->submit($form);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/pln/1/box/1', $this->client->getResponse()->headers->get('Location'));
        
        $this->client->request('GET', '/pln/1/box/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('127.0.0.1', $this->client->getResponse()->getContent());
        
        $box = $this->em->find('LOCKSSOMaticCrudBundle:Box', 1);
        $this->assertInstanceOf('LOCKSSOMatic\CrudBundle\Entity\Box', $box);
        $this->assertEquals('localhost', $box->getHostname());
        $this->assertEquals('TCP', $box->getProtocol());
        $this->assertEquals('127.0.0.1', $box->getIpAddress());
        $this->assertEquals(1234, $box->getPort());
        $this->assertEquals(12345, $box->getWebServicePort());
        
    }
}
