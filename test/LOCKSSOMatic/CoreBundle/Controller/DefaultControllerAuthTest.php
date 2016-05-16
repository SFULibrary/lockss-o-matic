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

}
