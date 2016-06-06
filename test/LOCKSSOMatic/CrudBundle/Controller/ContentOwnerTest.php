<?php

namespace LOCKSSOMatic\CrudBundle\Controller;

use LOCKSSOMatic\CoreBundle\Utilities\AbstractTestCase;

class ContentOwnerTest extends AbstractTestCase {

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
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPln',
            'LOCKSSOMatic\UserBundle\DataFixtures\ORM\test\LoadUser',
        );
    }
   
    public function testIndex() {
        $this->client->request('GET', '/contentowner/');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        
        $crawler = $this->client->getCrawler();
        $this->assertCount(1, $crawler->filter('table.table a'));
    }    
    
    public function testShow() {
        $this->client->request('GET', '/contentowner/1');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
    }
    
    public function testCreate() {
        $this->client->request('GET', '/contentowner/new');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $formCrawler = $this->client->getCrawler()->selectButton('Create');
        $form = $formCrawler->form(array(
            'lockssomatic_crudbundle_contentowner[name]' => 'Testing Owner',
            'lockssomatic_crudbundle_contentowner[emailAddress]' => 'co@example.com',
        ));
        $this->client->submit($form);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/contentowner/2', $this->client->getResponse()->headers->get('Location'));
        
        $this->em->clear();
        $owner = $this->em->find('LOCKSSOMaticCrudBundle:ContentOwner', 2);
        $this->assertInstanceOf('LOCKSSOMatic\CrudBundle\Entity\ContentOwner', $owner);
        $this->assertEquals('Testing Owner', $owner->getName());
        $this->assertEquals('co@example.com', $owner->getEmailAddress());
    }
    
    public function testEdit() {
        $this->client->request('GET', '/contentowner/1/edit');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $formCrawler = $this->client->getCrawler()->selectButton('Update');
        $form = $formCrawler->form(array(
            'lockssomatic_crudbundle_contentowner[name]' => 'Testing Owner',
            'lockssomatic_crudbundle_contentowner[emailAddress]' => 'co@example.com',
        ));
        $this->client->submit($form);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/contentowner/1', $this->client->getResponse()->headers->get('Location'));
        
        $this->em->clear();
        $owner = $this->em->find('LOCKSSOMaticCrudBundle:ContentOwner', 1);
        $this->assertInstanceOf('LOCKSSOMatic\CrudBundle\Entity\ContentOwner', $owner);
        $this->assertEquals('Testing Owner', $owner->getName());
        $this->assertEquals('co@example.com', $owner->getEmailAddress());
    }

    public function testDelete() {
        $this->client->request('GET', '/contentowner/1/delete');
        $response = $this->client->getResponse();
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/contentowner/', $this->client->getResponse()->headers->get('Location'));
        
        $this->em->clear();
        $owner = $this->em->find('LOCKSSOMaticCrudBundle:ContentOwner', 1);
        $this->assertNull($owner);
    }

}
