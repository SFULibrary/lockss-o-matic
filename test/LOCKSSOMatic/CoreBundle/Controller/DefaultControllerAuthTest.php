<?php

namespace LOCKSSOMatic\CoreBundle\Controller;

use LOCKSSOMatic\CoreBundle\Utilities\AbstractTestCase;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerAuthTest extends AbstractTestCase
{

    protected $client;

    public function setUp()
    {
        parent::setUp();
        $this->client = static::createClient(array(),
                array(
                'PHP_AUTH_USER' => 'admin@example.com',
                'PHP_AUTH_PW'   => 'supersecret',
        ));
    }

    public function fixtures()
    {
        return array(
            'LOCKSSOMatic\UserBundle\DataFixtures\ORM\test\LoadUser',
        );
    }

    public function testIndex()
    {
        $this->client->request('GET', '/');
        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }
    
    public function testMenus() {
        $this->client->request('GET', '/');
        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        
        $crawler = $this->client->getCrawler()->filter('#navbar > ul.nav');
        $this->assertCount(2, $crawler);
        $main = $crawler->eq(0);
        $this->assertCount(4, $main->children());
        
        $user = $crawler->eq(1);
        $this->assertCount(1, $user->children());
        // 0 is the head of the menu, contains everything.
        $this->assertEquals('Profile', trim($user->filter('li')->eq(1)->text()));
    }
}
