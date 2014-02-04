<?php

namespace LOCKSSOMatic\CRUDBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AusControllerTest extends WebTestCase
{
    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/aus/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /aus/");
        $crawler = $client->click($crawler->selectLink('Create a new entry')->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form(array(
            'lockssomatic_crudbundle_aus[plnsId]' => '1',
            'lockssomatic_crudbundle_aus[auid]' => '2',
            'lockssomatic_crudbundle_aus[manifestUrl]' => 'http://foo.com/manifest.htm',
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("foo.com")')->count(), 'Missing element td:contains("foo.com")');

        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array(
            'lockssomatic_crudbundle_aus[manifestUrl]' => 'http://bar.info/manifest.htm',
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains the string 'bar.info'
        $this->assertGreaterThan(0, $crawler->filter('html:contains("bar.info")')->count(), 'Missing string "bar.info"');

        // Delete the entity
        $client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();

        // Check the entity has been deleted from the list
        $this->assertNotRegExp('/bar\.info/', $client->getResponse()->getContent());
    }

}
