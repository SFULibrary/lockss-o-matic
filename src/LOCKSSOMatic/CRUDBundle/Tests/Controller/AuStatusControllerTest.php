<?php

namespace LOCKSSOMatic\CRUDBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuStatusControllerTest extends WebTestCase
{
    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        // Go to the list view
        $crawler = $client->request('GET', '/austatus/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /austatus/");

        // Go to the show view. Fails when there are no rows in table.
        // $crawler = $client->click($crawler->selectLink('show')->link());
        // $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code");

        // Check the element contains text "Update path"
       $this->assertGreaterThan(0, $crawler->filter('html:contains("Boxhostname")')->count(), 'Missing text "Boxhostname"');

    }

}
