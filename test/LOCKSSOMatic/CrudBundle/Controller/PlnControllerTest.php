<?php

namespace LOCKSSOMatic\CrudBundle\Controller;

use LOCKSSOMatic\CoreBundle\Utilities\AbstractTestCase;
use LOCKSSOMatic\CrudBundle\Entity\Box;
use Symfony\Bundle\FrameworkBundle\Client;

class PlnControllerTest extends AbstractTestCase {
    
    /**
     * @var Client
     */
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
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPln',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadKeyStore',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPlugin',            
            'LOCKSSOMatic\UserBundle\DataFixtures\ORM\test\LoadUser',
        );
    }
    
    public function testIndex() {
        $this->client->request('GET', '/pln/');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $crawler = $this->client->getCrawler();
        $this->assertCount(1, $crawler->filter('table.table a'));
    }
    
    public function testShow() {
        $this->client->request('GET', '/pln/1');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
    }
    
    public function testCreate() {
        $this->client->request('GET', '/pln/new');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $formCrawler = $this->client->getCrawler()->selectButton('Create');
        $form = $formCrawler->form(array(
            'lockssomatic_crudbundle_pln[name]' => 'Test Pln C',
            'lockssomatic_crudbundle_pln[description]' => 'Test description',
            'lockssomatic_crudbundle_pln[username]' => 'testc',
            'lockssomatic_crudbundle_pln[password]' => 'testp',
        ));
        $this->client->submit($form);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/pln/2', $this->client->getResponse()->headers->get('Location'));
        
        $this->em->clear(); // damn caching
        $pln = $this->em->find('LOCKSSOMaticCrudBundle:Pln', 2);
        $this->assertInstanceOf('LOCKSSOMatic\CrudBundle\Entity\Pln', $pln);
        $this->assertEquals('Test Pln C', $pln->getName());
        $this->assertEquals('Test description', $pln->getDescription());
        $this->assertEquals('testc', $pln->getUsername());
        $this->assertEquals('testp', $pln->getPassword());
    }
    
    public function testEdit() {
        $this->client->request('GET', '/pln/1/edit');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $formCrawler = $this->client->getCrawler()->selectButton('Update');
        $form = $formCrawler->form(array(
            'lockssomatic_crudbundle_pln[name]' => 'Test Pln C',
            'lockssomatic_crudbundle_pln[description]' => 'Test description',
            'lockssomatic_crudbundle_pln[username]' => 'testc',
            'lockssomatic_crudbundle_pln[password]' => 'testp',
        ));
        $this->client->submit($form);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/pln/1', $this->client->getResponse()->headers->get('Location'));
        
        $this->em->clear(); // damn caching
        $pln = $this->em->find('LOCKSSOMaticCrudBundle:Pln', 1);
        $this->assertInstanceOf('LOCKSSOMatic\CrudBundle\Entity\Pln', $pln);
        $this->assertEquals('Test Pln C', $pln->getName());
        $this->assertEquals('Test description', $pln->getDescription());
        $this->assertEquals('testc', $pln->getUsername());
        $this->assertEquals('testp', $pln->getPassword());
    }
    
    /**
     * Boxes and deposits and aus etc. have not been loaded, so the delete
     * should succeed.
     */
    public function testDelete() {
        $this->client->request('GET', '/pln/1/delete');
        $response = $this->client->getResponse();
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/pln/', $this->client->getResponse()->headers->get('Location'));
        
        $this->em->clear();
        $this->assertNull($this->em->find('LOCKSSOMaticCrudBundle:Pln', 1));
    }
    
    /**
     * Boxes and deposits and aus etc. have not been loaded, so the delete
     * should succeed.
     */
    public function testDelete404() {
        $this->client->request('GET', '/pln/3/delete');
        $response = $this->client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
    }
    
    public function testShowAccess() {
        $this->client->request('GET', '/pln/1/access');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        
        $crawler = $this->client->getCrawler();
        $table = $crawler->filter('table.table')->first();
        
        // one row for headers, one for admin@ and one for user@
        $this->assertCount(3, $table->filter('tr'));        
    }
    
    public function testShowAccess404() {
        $this->client->request('GET', '/pln/3/access');
        $response = $this->client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
    }
    
    public function testEditAccess() {
        $this->client->request('GET', '/pln/1/access/edit');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        
        $crawler = $this->client->getCrawler();
        $formCrawler = $crawler->selectButton('Update');
        $form = $formCrawler->form(array(
            'form[user_2]' => 'DEPOSIT',
        ));
        $this->client->submit($form);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/pln/1/access', $this->client->getResponse()->headers->get('Location'));
    }
    
    public function testEditAccess404() {
        $this->client->request('GET', '/pln/3/access/edit');
        $response = $this->client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
    }
    
    public function testShowPlugins() {
        $this->client->request('GET', '/pln/1/plugins');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        
        $crawler = $this->client->getCrawler();
        $table = $crawler->filter('table.table')->first();
        $this->assertCount(2, $table->filter('tr'));        
    }
    
    public function testShowPlugins404() {
        $this->client->request('GET', '/pln/3/plugins');
        $response = $this->client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
    }
    
    public function testKeystore() {
        $this->client->request('GET', '/pln/1/keystore');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('test.keystore', $response->getContent());
    }
    
    public function testKeystore404() {
        $this->client->request('GET', '/pln/3/keystore');
        $response = $this->client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());        
    }
    
}
