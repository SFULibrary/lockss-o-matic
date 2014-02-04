<?php

namespace LOCKSSOMatic\CRUDBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PlnsControllerTest extends WebTestCase
{
    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/plns/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /plns/");
        $crawler = $client->click($crawler->selectLink('Create a new entry')->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form(array(
            'lockssomatic_crudbundle_plns[name]'  => 'Test name',
            'lockssomatic_crudbundle_plns[propsPath]'  => 'Test props path',
            // ... other fields to fill
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Test name")')->count(), 'Missing element td:contains("Test name")');

        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array(
            'lockssomatic_crudbundle_plns[name]'  => 'Updated name',
            'lockssomatic_crudbundle_plns[propsPath]'  => 'Updated path',
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains text "Updated path"
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Updated path")')->count(), 'Missing text "Updated path"');

        // Delete the entity
        $client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();

        // Check the entity has been deleted from the list
        $this->assertNotRegExp("/Updated\spath/", $client->getResponse()->getContent());
    }

}
