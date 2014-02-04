<?php

namespace LOCKSSOMatic\CRUDBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContentControllerTest extends WebTestCase
{
    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        // Go to the list view
        $crawler = $client->request('GET', '/content/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /content/");

        // Go to the show view. Fails when there are no rows in the table.
        // $crawler = $client->click($crawler->selectLink('show')->link());
        // $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code");

        // Check the element contains text "Update path"
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Checksumtype")')->count(), 'Missing text "Checksumtype"');
    }

}
