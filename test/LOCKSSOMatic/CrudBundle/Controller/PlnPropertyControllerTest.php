<?php

namespace LOCKSSOMatic\CrudBundle\Controller;

use LOCKSSOMatic\CoreBundle\Utilities\AbstractTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class PlnPropertyControllerTest extends AbstractTestCase {

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
            'LOCKSSOMatic\CrudBundle\DataFixtures\ORM\test\LoadPlugin',
            'LOCKSSOMatic\UserBundle\DataFixtures\ORM\test\LoadUser',
        );
    }

    public function testIndex() {
        $this->client->request('GET', '/pln/1/property/');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $crawler = $this->client->getCrawler();
        // one row for header, one for each of two properties.
        $this->assertCount(3, $crawler->filter('table.table tr'));
    }

    public function testNewString() {
        $this->client->request('GET', '/pln/1/property/new');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $formCrawler = $this->client->getCrawler()->selectButton('Create');
        $form = $formCrawler->form(array(
            'lockssomatic_crudbundle_plnproperty[name]' => 'test.prop',
            'lockssomatic_crudbundle_plnproperty[value][0]' => 'Test string',
        ));
        $this->client->submit($form);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/pln/1/property/', $this->client->getResponse()->headers->get('Location'));
        
        $this->em->clear();
        $pln = $this->em->find('LOCKSSOMaticCrudBundle:Pln', 1);
        $this->assertEquals('Test string', $pln->getProperty('test.prop'));
    }

    public function testNewList() {
        $this->client->request('GET', '/pln/1/property/new');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $formCrawler = $this->client->getCrawler()->selectButton('Create');
        $form = $formCrawler->form();
        $form->setValues(array(
            'lockssomatic_crudbundle_plnproperty[name]' => 'test.list.prop',
            'lockssomatic_crudbundle_plnproperty[value][0]' => 'Test string',
        ));
        $values = $form->getPhpValues();
        $values['lockssomatic_crudbundle_plnproperty']['value'][1] = 'Other string';
        $this->client->request($form->getMethod(), $form->getUri(), $values);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/pln/1/property/', $this->client->getResponse()->headers->get('Location'));
        
        $this->em->clear();
        $pln = $this->em->find('LOCKSSOMaticCrudBundle:Pln', 1);
        $this->assertEquals(array('Test string', 'Other string'), $pln->getProperty('test.list.prop'));
    }

    public function testEditString() {
        $this->client->request('GET', '/pln/1/property/ca.sfu.test.value/edit');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $formCrawler = $this->client->getCrawler()->selectButton('Update');
        $form = $formCrawler->form(array(
            'lockssomatic_crudbundle_plnproperty[name]' => 'test.prop',
            'lockssomatic_crudbundle_plnproperty[value][0]' => 'Test string',
        ));
        $this->client->submit($form);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/pln/1/property/', $this->client->getResponse()->headers->get('Location'));
        
        $this->em->clear();
        $pln = $this->em->find('LOCKSSOMaticCrudBundle:Pln', 1);
        $this->assertEquals('Test string', $pln->getProperty('test.prop'));
    }

    public function testEditList() {
        $this->client->request('GET', '/pln/1/property/ca.sfu.test.list/edit');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $formCrawler = $this->client->getCrawler()->selectButton('Update');
        $form = $formCrawler->form();
        $form->setValues(array(
            'lockssomatic_crudbundle_plnproperty[name]' => 'test.list.prop',
            'lockssomatic_crudbundle_plnproperty[value][0]' => 'Test string',
        ));
        $values = $form->getPhpValues();
        $values['lockssomatic_crudbundle_plnproperty']['value'][1] = 'Other string';
        $this->client->request($form->getMethod(), $form->getUri(), $values);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/pln/1/property/', $this->client->getResponse()->headers->get('Location'));
        
        $this->em->clear();
        $pln = $this->em->find('LOCKSSOMaticCrudBundle:Pln', 1);
        $this->assertEquals(array('Test string', 'Other string', 'item 3'), $pln->getProperty('test.list.prop'));
    }
}
