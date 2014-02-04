<?php

namespace LOCKSSOMatic\CRUDBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BoxesControllerTest extends WebTestCase
{
    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/boxes/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /boxes/");
        $crawler = $client->click($crawler->selectLink('Create a new entry')->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form(array(
            'lockssomatic_crudbundle_boxes[plnsId]' => '1',
            'lockssomatic_crudbundle_boxes[hostname]' => 'lockss1.example.com',
            'lockssomatic_crudbundle_boxes[ipAddress]' => '123.456.789.123',
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("lockss1")')->count(), 'Missing element td:contains("lockss1")');

        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array(
            'lockssomatic_crudbundle_boxes[hostname]' => 'lockss2.example.com',
            'lockssomatic_crudbundle_boxes[ipAddress]' => '222.333.444.555',
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains the string 'lockss2'
        $this->assertGreaterThan(0, $crawler->filter('html:contains("lockss2")')->count(), 'Missing string "bar.info"');

        // Delete the entity
        $client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();

        // Check the entity has been deleted from the list
        $this->assertNotRegExp('/lockss2/', $client->getResponse()->getContent());
    }

}
