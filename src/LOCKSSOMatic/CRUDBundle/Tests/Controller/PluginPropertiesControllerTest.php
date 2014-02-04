<?php

namespace LOCKSSOMatic\CRUDBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PluginPropertiesControllerTest extends WebTestCase
{
    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/pluginproperties/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /pluginproperties/");
        $crawler = $client->click($crawler->selectLink('Create a new entry')->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form(array(
            'lockssomatic_crudbundle_pluginproperties[pluginsId]' => '1',
            'lockssomatic_crudbundle_pluginproperties[ausId]' => '1',
            'lockssomatic_crudbundle_pluginproperties[parentId]' => '1',
            'lockssomatic_crudbundle_pluginproperties[propertyKey]' => 'foo',
            'lockssomatic_crudbundle_pluginproperties[propertyValue]' => 'bar',
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("bar")')->count(), 'Missing element td:contains("bar")');

        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array(
            'lockssomatic_crudbundle_pluginproperties[propertyKey]' => 'new foo',
            'lockssomatic_crudbundle_pluginproperties[propertyValue]' => 'new bar',
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains the string 'new foo'.
        $this->assertGreaterThan(0, $crawler->filter('html:contains("new foo")')->count(), 'Missing string "new foo"');

        // Delete the entity
        $client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();

        // Check the entity has been deleted from the list
        $this->assertNotRegExp('/new\sfoo/', $client->getResponse()->getContent());
    }

}
