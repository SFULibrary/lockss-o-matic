<?php

namespace LOCKSSOMatic\SwordBundle\Tests;

use LOCKSSOMatic\CoreBundle\Utilities\AbstractTestCase;
use LOCKSSOMatic\CrudBundle\Entity\Deposit;
use LOCKSSOMatic\SwordBundle\Utilities\Namespaces;
use SimpleXMLElement;

class SwordControllerTest extends AbstractTestCase
{

    /**
     * @var Namespaces
     */
    private $namespaces;

    public function __construct()
    {
        parent::__construct();
        $this->namespaces = new Namespaces();
    }

    public function setUp()
    {
        parent::setUp();
    }

    private function getXml($string)
    {
        $xml = new SimpleXMLElement($string);
        $this->namespaces->registerNamespaces($xml);
        return $xml;
    }

    private function assertXpath(SimpleXMLElement $xml, $expected, $xpath, $method = 'assertEquals')
    {
        $this->$method($expected, (string)($xml->xpath($xpath)[0]));
    }

    public function testServiceDocumentMissingOnBehalfOf()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/sword/2.0/sd-iri');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $xml = $this->getXml($client->getResponse()->getContent());
        $this->assertXpath(
            $xml,
            '400 - Required HTTP header On-Behalf-Of missing.',
            '/sword:error/atom:summary'
        );
    }

    public function testServiceDocument()
    {
        $provider = $this->references->getReference('provider');
        $client = static::createClient();
        $crawler = $client->request(
            'GET',
            '/api/sword/2.0/sd-iri',
            array(),
            array(),
            array(
                'HTTP_On-Behalf-Of' => $provider->getUuid(),
            )
        );
        //$this->assertEquals('', $client->getResponse()->getContent());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $xml = $this->getXml($client->getResponse()->getContent());
        $this->assertXpath($xml, 2, '/app:service/sword:version');
        $this->assertXpath($xml, '10000', '/app:service/sword:maxUploadSize');
        $this->assertXpath($xml, 'SHA-1 MD5', '/app:service/lom:uploadChecksumType');
        $this->assertXpath($xml, $provider->getUuid(), '/app:service/app:workspace/app:collection/@href', 'assertStringEndsWith');
        $this->assertXpath($xml, 'ca.sfu.test', '//lom:pluginIdentifier/@id');
        $this->assertEquals(1, count($xml->xpath('//lom:property')));
        $this->assertXpath($xml, 'base_url', '//lom:property[1]/@name');
    }


    public function testCreateDepositMissingParam()
    {
        $provider = $this->references->getReference('provider');
        $xmlStr = file_get_contents(dirname(__FILE__) . '/data/depositMissingParam.xml');
        $client = static::createClient();
        $crawler = $client->request(
            'POST',
            '/api/sword/2.0/col-iri/' . $provider->getUuid(),
            array(),
            array(),
            array('Content-Type' => 'application/xml'),
            $xmlStr
        );
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
        $xml = $this->getXml($response->getContent());
        $this->assertXpath(
            $xml,
            '400 - base_url is a required property.',
            '//atom:summary'
        );
    }

    public function testCreateDepositMismatchedUrl()
    {
        $provider = $this->references->getReference('provider');
        $xmlStr = file_get_contents(dirname(__FILE__) . '/data/depositMismatchedHosts.xml');
        $client = static::createClient();
        $crawler = $client->request(
            'POST',
            '/api/sword/2.0/col-iri/' . $provider->getUuid(),
            array(),
            array(),
            array('Content-Type' => 'application/xml'),
            $xmlStr
        );
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
        $xml = $this->getXml($response->getContent());
        $this->assertXpath(
            $xml,
            '400 - Content URL does not match a corresponding LOCKSS permission URL.',
            '//atom:summary',
            'assertStringStartsWith'
        );
    }

    public function testCreateDeposit()
    {
        $provider = $this->references->getReference('provider');
        $xmlStr = file_get_contents(dirname(__FILE__) . '/data/depositSingle.xml');
        $client = static::createClient();
        $crawler = $client->request(
            'POST',
            '/api/sword/2.0/col-iri/' . $provider->getUuid(),
            array(),
            array(),
            array('Content-Type' => 'application/xml'),
            $xmlStr
        );
        $response = $client->getResponse();
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals(
            'http://localhost/api/sword/2.0/cont-iri/473A1B0D-425F-417B-94CF-28C3FC04B0E2/066D5E90-03F7-469E-A231-C67FB8D6109F/edit',
            $response->headers->get('Location')
        );

        $xml = $this->getXml($response->getContent());
        $this->assertXpath(
            $xml,
            'http://localhost/api/sword/2.0/col-iri/473A1B0D-425F-417B-94CF-28C3FC04B0E2',
            '//atom:link[@rel="edit-media"]/@href'
        );
        $this->assertXpath(
            $xml,
            'http://localhost/api/sword/2.0/cont-iri/473A1B0D-425F-417B-94CF-28C3FC04B0E2/066D5E90-03F7-469E-A231-C67FB8D6109F/edit',
            '//atom:link[@rel="http://purl.org/net/sword/terms/add"]/@href'
        );
        $this->assertXpath(
            $xml,
            'http://localhost/api/sword/2.0/cont-iri/473A1B0D-425F-417B-94CF-28C3FC04B0E2/066D5E90-03F7-469E-A231-C67FB8D6109F/edit',
            '//atom:link[@rel="edit"]/@href'
        );
        $this->assertXPath(
            $xml,
            'http://localhost/api/sword/2.0/cont-iri/473A1B0D-425F-417B-94CF-28C3FC04B0E2/066D5E90-03F7-469E-A231-C67FB8D6109F/state',
            '//atom:link[@rel="http://purl.org/net/sword/terms/statement"]/@href'
        );

        $repo = $this->em->getRepository('LOCKSSOMaticCrudBundle:Deposit');
        /** @var Deposit $deposit */
        $deposit = $repo->find(2);
        $this->assertNotNull($deposit);
        $this->assertEquals('066D5E90-03F7-469E-A231-C67FB8D6109F', $deposit->getUuid());
        $this->assertEquals('Image samples from the internet', $deposit->getTitle());
        $content = $deposit->getContent();
        $this->assertEquals(1, count($content));
        $this->assertEquals('http://example.com/3691/11186563486_8796f4f843_o_d.jpg', $content[0]->getUrl());
    }

    public function testCreateDepositMultipleContent()
    {
        $provider = $this->references->getReference('provider');
        $xmlStr = file_get_contents(dirname(__FILE__) . '/data/depositMultiple.xml');
        $client = static::createClient();
        $crawler = $client->request(
            'POST',
            '/api/sword/2.0/col-iri/' . $provider->getUuid(),
            array(),
            array(),
            array('Content-Type' => 'application/xml'),
            $xmlStr
        );
        $response = $client->getResponse();
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals(
            'http://localhost/api/sword/2.0/cont-iri/473A1B0D-425F-417B-94CF-28C3FC04B0E2/066D5E90-03F7-469E-A231-C67FB8D6109F/edit',
            $response->headers->get('Location')
        );

        $xml = $this->getXml($response->getContent());
        $this->assertXpath(
            $xml,
            'http://localhost/api/sword/2.0/col-iri/473A1B0D-425F-417B-94CF-28C3FC04B0E2',
            '//atom:link[@rel="edit-media"]/@href'
        );
        $this->assertXpath(
            $xml,
            'http://localhost/api/sword/2.0/cont-iri/473A1B0D-425F-417B-94CF-28C3FC04B0E2/066D5E90-03F7-469E-A231-C67FB8D6109F/edit',
            '//atom:link[@rel="http://purl.org/net/sword/terms/add"]/@href'
        );
        $this->assertXpath(
            $xml,
            'http://localhost/api/sword/2.0/cont-iri/473A1B0D-425F-417B-94CF-28C3FC04B0E2/066D5E90-03F7-469E-A231-C67FB8D6109F/edit',
            '//atom:link[@rel="edit"]/@href'
        );
        $this->assertXPath(
            $xml,
            'http://localhost/api/sword/2.0/cont-iri/473A1B0D-425F-417B-94CF-28C3FC04B0E2/066D5E90-03F7-469E-A231-C67FB8D6109F/state',
            '//atom:link[@rel="http://purl.org/net/sword/terms/statement"]/@href'
        );

        $repo = $this->em->getRepository('LOCKSSOMaticCrudBundle:Deposit');
        /** @var Deposit $deposit */
        $deposit = $repo->find(2);
        $this->assertNotNull($deposit);
        $this->assertEquals('066D5E90-03F7-469E-A231-C67FB8D6109F', $deposit->getUuid());
        $this->assertEquals('Image samples from the internet', $deposit->getTitle());
        $content = $deposit->getContent();
        $this->assertEquals(3, count($content));
        $this->assertEquals('http://example.com/3691/11186563486_8796f4f843_o_d.jpg', $content[0]->getUrl());

        $this->assertEquals('http://example.com/wm/paint/auth/gauguin/gauguin.alyscamps.jpg', $content[1]->getUrl());
        $this->assertEquals('http://example.com/wm/paint/auth/monet/parliament/parliament.jpg', $content[2]->getUrl());
    }

    public function testEditDeposit()
    {

    }

    public function testStatement()
    {
        
    }
}
