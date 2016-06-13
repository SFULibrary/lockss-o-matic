<?php

namespace LOCKSSOMatic\CrudBundle\Controller;

use LOCKSSOMatic\CoreBundle\Utilities\AbstractTestCase;

class ContentProviderTest extends AbstractTestCase {

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
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPlugin',
            'LOCKSSOMatic\UserBundle\DataFixtures\ORM\test\LoadUser',
        );
    }
   
    public function testIndex() {
        $this->client->request('GET', '/contentprovider/');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        
        $crawler = $this->client->getCrawler();
        $this->assertCount(1, $crawler->filter('table.table a'));
    }    
    
    public function testShow() {
        $this->client->request('GET', '/contentprovider/1');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
    }
    
    public function testCreate() {
        $this->client->request('GET', '/contentprovider/new');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $formCrawler = $this->client->getCrawler()->selectButton('Create');
        $form = $formCrawler->form(array(
            'lockssomatic_crudbundle_contentprovider[uuid]' => '',
            'lockssomatic_crudbundle_contentprovider[permissionurl]' => 'http://example.com/path/path/path/permission',
            'lockssomatic_crudbundle_contentprovider[name]' => 'New Test Provider',
            'lockssomatic_crudbundle_contentprovider[maxFileSize]' => '1234',
            'lockssomatic_crudbundle_contentprovider[maxAuSize]' => '12345',
            'lockssomatic_crudbundle_contentprovider[contentOwner]' => '1',
            'lockssomatic_crudbundle_contentprovider[plugin]' => '1',
        ));
        $this->client->submit($form);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/contentprovider/2', $this->client->getResponse()->headers->get('Location'));
        
        $this->em->clear();
        $owner = $this->em->find('LOCKSSOMaticCrudBundle:ContentProvider', 2);
        $this->assertInstanceOf('LOCKSSOMatic\CrudBundle\Entity\ContentProvider', $owner);
        $this->assertEquals('New Test Provider', $owner->getName());
        $this->assertEquals(36, strlen($owner->getUuid()));
    }
    
    public function testCreateUuid() {
        $this->client->request('GET', '/contentprovider/new');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $formCrawler = $this->client->getCrawler()->selectButton('Create');
        $form = $formCrawler->form(array(
            'lockssomatic_crudbundle_contentprovider[uuid]' => '7FA079D4-8BBE-48A5-BB67-D5AF861DB6D4',
            'lockssomatic_crudbundle_contentprovider[permissionurl]' => 'http://example.com/path/path/path/permission',
            'lockssomatic_crudbundle_contentprovider[name]' => 'New Test Provider',
            'lockssomatic_crudbundle_contentprovider[maxFileSize]' => '1234',
            'lockssomatic_crudbundle_contentprovider[maxAuSize]' => '12345',
            'lockssomatic_crudbundle_contentprovider[contentOwner]' => '1',
            'lockssomatic_crudbundle_contentprovider[plugin]' => '1',
        ));
        $this->client->submit($form);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/contentprovider/2', $this->client->getResponse()->headers->get('Location'));
        
        $this->em->clear();
        $owner = $this->em->find('LOCKSSOMaticCrudBundle:ContentProvider', 2);
        $this->assertInstanceOf('LOCKSSOMatic\CrudBundle\Entity\ContentProvider', $owner);
        $this->assertEquals('New Test Provider', $owner->getName());
        $this->assertEquals('7FA079D4-8BBE-48A5-BB67-D5AF861DB6D4', $owner->getUuid());
    }
    
    public function testEdit() {
        $this->client->request('GET', '/contentprovider/1/edit');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $formCrawler = $this->client->getCrawler()->selectButton('Update');
        $form = $formCrawler->form(array(
            'lockssomatic_crudbundle_contentprovider[permissionurl]' => 'http://example.com/path/path/path/permission',
            'lockssomatic_crudbundle_contentprovider[name]' => 'New Test Provider',
            'lockssomatic_crudbundle_contentprovider[maxFileSize]' => '1234',
            'lockssomatic_crudbundle_contentprovider[maxAuSize]' => '12345',
            'lockssomatic_crudbundle_contentprovider[contentOwner]' => '1',
            'lockssomatic_crudbundle_contentprovider[plugin]' => '1',
        ));
        $this->client->submit($form);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/contentprovider/1', $this->client->getResponse()->headers->get('Location'));
        
        $this->em->clear();
        $owner = $this->em->find('LOCKSSOMaticCrudBundle:ContentProvider', 1);
        $this->assertInstanceOf('LOCKSSOMatic\CrudBundle\Entity\ContentProvider', $owner);
        $this->assertEquals('New Test Provider', $owner->getName());
        $this->assertEquals(36, strlen($owner->getUuid()));
    }

    public function testDelete() {
        $this->client->request('GET', '/contentprovider/1/delete');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/contentprovider/', $this->client->getResponse()->headers->get('Location'));
        
        $this->em->clear();
        $owner = $this->em->find('LOCKSSOMaticCrudBundle:ContentProvider', 1);
        $this->assertNull($owner);
    }

}
