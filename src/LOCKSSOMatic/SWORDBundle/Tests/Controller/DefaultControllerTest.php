<?php

namespace LOCKSSOMatic\SWORDBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testServiceDocument()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/api/sword/2.0/sd-iri');

        $this->assertRegExp('/<sword:version>2\.0<\/sword:version>/', $client->getResponse()->getContent());
    }
}
