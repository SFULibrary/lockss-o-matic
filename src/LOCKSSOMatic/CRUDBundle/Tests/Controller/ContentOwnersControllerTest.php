<?php

namespace LOCKSSOMatic\CRUDBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContentOwnersControllerTest extends WebTestCase
{
    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/contentowners/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /contentowners/");
        $crawler = $client->click($crawler->selectLink('Create a new entry')->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form(array(
            'lockssomatic_crudbundle_contentowners[name]'  => 'Owner name',
            'lockssomatic_crudbundle_contentowners[emailAddress]'  => 'me@example.com',
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("me@example.com")')->count(), 'Missing element td:contains("me@example.com")');

        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array(
            'lockssomatic_crudbundle_contentowners[emailAddress]'  => 'mynewaddress@foo.com',
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the html contains the updated email adddress
        $this->assertGreaterThan(0, $crawler->filter('html:contains("mynewaddress")')->count(), 'Missing string "mynewaddress"');

        // Delete the entity
        $client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();

        // Check the entity has been deleted from the list
        $this->assertNotRegExp('/mynewaddress/', $client->getResponse()->getContent());
    }

}
