<?php

namespace LOCKSSOMatic\CRUDBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuPropertiesControllerTest extends WebTestCase
{
    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/auproperties/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /auproperties/");
        $crawler = $client->click($crawler->selectLink('Create a new entry')->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form(array(
            'lockssomatic_crudbundle_auproperties[ausId]' => '1',
            'lockssomatic_crudbundle_auproperties[parentId]' => '1',
            'lockssomatic_crudbundle_auproperties[propertyKey]' => 'new key',
            'lockssomatic_crudbundle_auproperties[propertyValue]' => 'new value',
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("new key")')->count(), 'Missing element td:contains("new key")');

        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array(
            'lockssomatic_crudbundle_auproperties[propertyKey]' => 'updated key',
            'lockssomatic_crudbundle_auproperties[propertyValue]' => 'updated value',
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains the string 'updated value'
        $this->assertGreaterThan(0, $crawler->filter('html:contains("updated value")')->count(), 'Missing string "updated value"');

        // Delete the entity
        $client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();

        // Check the entity has been deleted from the list
        $this->assertNotRegExp('/updated\value/', $client->getResponse()->getContent());
    }

}
