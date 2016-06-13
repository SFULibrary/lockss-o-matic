<?php

namespace LOCKSSOMatic\CrudBundle\Controller;

use LOCKSSOMatic\CoreBundle\Utilities\AbstractTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class ExtendedDepositControllerTest extends AbstractTestCase {

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
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadDeposit',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPln',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPlugin',
            'LOCKSSOMatic\UserBundle\DataFixtures\ORM\test\LoadUser',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadExtraContentProvider',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadExtraPln',
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadExtraDeposit',
        );
    }

    /**
     * Make sure the deposits in PLN 2 don't bleed over into PLN 1.
     */
    public function testIndexPln1() {
        $this->client->request('GET', '/pln/1/deposit/');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $crawler = $this->client->getCrawler();
        $this->assertCount(2, $crawler->filter('table.table a'));
    }
    
    public function testIndexPln2() {        
        $this->client->request('GET', '/pln/2/deposit/');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
    }
    
    public function testIndexPln404() {
        $this->client->request('GET', '/pln/3/deposit/');
        $response = $this->client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
    }
    
    public function testShowDeposit1() {
        $this->client->request('GET', '/pln/1/deposit/1');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());        
    }
    
    public function testShowDeposit2() {
        $this->client->request('GET', '/pln/1/deposit/2');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());        
    }
    
    public function testShowDeposit3() {
        $this->client->request('GET', '/pln/2/deposit/3');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());        
    }
    
    public function testShowDeposit3WrongPln() {
        $this->client->request('GET', '/pln/1/deposit/3');
        $response = $this->client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());        
    }
    
}
