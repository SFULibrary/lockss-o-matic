<?php

namespace LOCKSSOMatic\SWORDBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends WebTestCase {

    private static $namespaces = array(
        'dcterms' => "http://purl.org/dc/terms/",
        'sword' => "http://purl.org/net/sword/terms/",
        'atom' => "http://www.w3.org/2005/Atom",
        'lom' => "http://lockssomatic.info/SWORD2",
        'rdf' => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#',
        'app' => "http://www.w3.org/2007/app",
    );

    // MUST CREATE A CONTENT PROVIDER ID FOR THIS TO WORK.
    public function testServiceDocument() {
        $client = static::createClient();

        $crawler = $client->request(
                'GET', 
                '/api/sword/2.0/sd-iri', 
                array(), 
                array(), 
                array('HTTP_X-On-Behalf-Of' => 1)
        );

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $xml = new \SimpleXMLElement($response->getContent());
        foreach (self::$namespaces as $k => $v) {
            $xml->registerXPathNamespace($k, $v);
        }

        $this->assertEquals("2.0", $xml->xpath('/app:service/sword:version/text()')[0]);
        $this->assertGreaterThan(1, $xml->xpath('/app:service/sword:maxUploadSize/text()')[0]);
        $this->assertEquals('md5', $xml->xpath('/app:service/lom:uploadChecksumType/text()')[0]);
        $href = $xml->xpath('//app:collection/@href')[0];
        $this->assertStringEndsWith('/api/sword/2.0/col-iri/1', (string)$href);
        $accept = $xml->xpath('//app:collection/app:accept/text()')[0];
        $this->assertTrue(strlen($accept) > 0);
        $this->assertEquals('true', $xml->xpath('//app:collection/sword:mediation/text()')[0]);
    }

//    public function testServiceDocumentNoBehalfOf() {
//        $client = static::createClient();
//
//        $crawler = $client->request(
//                'GET', 
//                '/api/sword/2.0/sd-iri'
//        );
//
//        $response = $client->getResponse();
//        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
//
//        $xml = new \SimpleXMLElement($response->getContent());
//        foreach (self::$namespaces as $k => $v) {
//            $xml->registerXPathNamespace($k, $v);
//        }
//
//        $this->assertEquals("2.0", $xml->xpath('/app:service/sword:version/text()')[0]);
//        $this->assertGreaterThan(1, $xml->xpath('/app:service/sword:maxUploadSize/text()')[0]);
//        $this->assertEquals('md5', $xml->xpath('/app:service/lom:uploadChecksumType/text()')[0]);
//    }
    
    
    
}
