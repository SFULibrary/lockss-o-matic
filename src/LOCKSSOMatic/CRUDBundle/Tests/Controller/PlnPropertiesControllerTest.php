<?php

namespace LOCKSSOMatic\CRUDBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PlnPropertiesControllerTest extends WebTestCase
{
    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/plnproperties/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /plnproperties/");
        $crawler = $client->click($crawler->selectLink('Create a new entry')->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form(array(
            'lockssomatic_crudbundle_plnproperties[plnsId]' => '1',
            'lockssomatic_crudbundle_plnproperties[parentId]' => '1',
            'lockssomatic_crudbundle_plnproperties[propertyKey]' => 'foo',
            'lockssomatic_crudbundle_plnproperties[propertyValue]' => 'bar',
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("foo")')->count(), 'Missing element td:contains("foo")');

        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array(
            'lockssomatic_crudbundle_plnproperties[propertyKey]' => 'updated key',
            'lockssomatic_crudbundle_plnproperties[propertyValue]' => 'update value',
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains the string 'updated key'
        $this->assertGreaterThan(0, $crawler->filter('html:contains("updated key")')->count(), 'Missing string "updated key"');

        // Delete the entity
        $client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();

        // Check the entity has been deleted from the list
        $this->assertNotRegExp('/updated\skey/', $client->getResponse()->getContent());
    }

}
