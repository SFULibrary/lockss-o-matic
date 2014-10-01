<?php

namespace LOCKSSOMatic\SWORDBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

use J20\Uuid\Uuid;

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
    
    //6.3.3. Creating a Resource with an Atom Entry
    public function testCreateResource() {
        
        $xml = new \SimpleXMLElement('<entry xmlns="http://www.w3.org/2005/Atom"/>');
        $uuid = Uuid::v4();
        foreach (self::$namespaces as $k => $v) {
            $xml->registerXPathNamespace($k, $v);
        }
        $xml->addChild('title', 'Image taken from page 275 of "The Youth\'s History of the United States, etc"');
        $xml->addChild('id', 'urn:uuid:' . $uuid);
        $xml->addChild('updated', date('c'));
        $author = $xml->addChild('author');
        $author->addChild('name', 'Ellis, Edward Sylvester');
        $summary = $xml->addChild('summary', 'Image taken from page 275 of "The Youth\'s History of the United States, etc"');
        $summary->addAttribute('type', 'text');
        
        $content = $xml->addChild('content', 'https://farm4.staticflickr.com/3691/11186563486_8796f4f843_o_d.jpg', 'http://lockssomatic.info/SWORD2');
        $content->addAttribute('size', '899922');
        $content->addAttribute('checksumType', 'md5');
        $content->addAttribute('checksumValue', 'ed5697c06b97f95e1221f857a3c08661');

        $xml->addChild('abstract', 'Image taken from page 275 of "The Youth\'s History of the United States, etc"', 'http://purl.org/dc/terms/');
        $xml->addChild('title', 'The Youth\'s History of the United States, etc', 'http://purl.org/dc/terms/');
        $xml->addChild('author', 'Ellis, Edward Sylvester', 'http://purl.org/dc/terms/');
        $xml->addChild('identifier', '001059471', 'http://purl.org/dc/terms/');
        $xml->addChild('identifier', 'British Library HMNTS 9605.f.8.', 'http://purl.org/dc/terms/');
        $xml->addChild('publisher', 'Cassell &amp; Co.', 'http://purl.org/dc/terms/');
        
        $client = static::createClient();
        $crawler = $client->request('POST', '/api/sword/2.0/col-iri/1', array(), array(), array(), $xml->asXML());
        $response = $client->getResponse();
        
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertTrue($response->headers->has('Location'));
        $this->assertStringEndsWith($uuid . '/edit', $response->headers->get('location'));
        
        $xml = new \SimpleXMLElement($response->getContent());
        foreach (self::$namespaces as $k => $v) {
            $xml->registerXPathNamespace($k, $v);
        }
    }
    
}
