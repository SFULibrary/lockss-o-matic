<?php

namespace LOCKSSOMatic\SWORDBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

use DOMDocument;
use DOMElement;
use DOMXPath;

use J20\Uuid\Uuid;

class DefaultControllerTest extends WebTestCase {

    private static $NS = array(
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

        $doc = new DOMDocument();
        $doc->loadXML($response->getContent());
        $xpath = new DOMXpath($doc);
        
        foreach (self::$NS as $k => $v) {
            $xpath->registerNamespace($k, $v);
        }

        $this->assertEquals("2.0", $xpath->query('/app:service/sword:version/text()')->item(0)->nodeValue);
        $this->assertGreaterThan(1, $xpath->query('/app:service/sword:maxUploadSize/text()')->item(0)->nodeValue);
        $this->assertEquals('md5', $xpath->query('/app:service/lom:uploadChecksumType/text()')->item(0)->nodeValue);
        
        $href = $xpath->query('//app:collection/@href')->item(0)->nodeValue;
        $this->assertStringEndsWith('/api/sword/2.0/col-iri/1', $href);
        $accept = $xpath->query('//app:collection/app:accept/text()')->item(0)->nodeValue;
        $this->assertTrue(strlen($accept) > 0);
        $this->assertEquals('true', $xpath->query('//app:collection/sword:mediation/text()')->item(0)->nodeValue);
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
    public function testCreateSingleResource() {
        $uuid = Uuid::v4();
        
        $doc = new DOMDocument("1.0", "UTF-8");
        $entry = $doc->appendChild($doc->createElementNS(self::$NS['atom'], 'entry'));
        foreach(self::$NS as $k => $v) {
            $ns = $entry->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:' . $k, $v);
        }
        
        $entry->appendChild(new DOMElement('title', 'Image taken from page 275 of "The Youth\'s History of the United States, etc"'));
        $entry->appendChild(new DOMElement('id', $uuid));
        $entry->appendChild(new DOMElement('updated', date('c')));
        $author = $entry->appendChild(new DOMElement('author'));
        $name = $author->appendChild(new DOMElement('name', 'Ellis, Edward Sylvester'));
        $summary = $entry->appendChild(new DOMElement('summary', 'Image taken from page 275 of "The Youth\'s History of the United States, etc"'));
        $summary->setAttribute('type', 'text');

        $content = $entry->appendChild(new DOMElement('lom:content', 'https://farm4.staticflickr.com/3691/11186563486_8796f4f843_o_d.jpg', self::$NS['lom']));
        $content->setAttribute('size', '899922');
        $content->setAttribute('checksumType', 'md5');
        $content->setAttribute('checksumValue', 'ed5697c06b97f95e1221f857a3c08661');

        $content->appendChild(new DOMElement('dcterms:abstract', 'Image taken from page 275 of "The Youth\'s History of the United States, etc"', self::$NS['dcterms']));
        $content->appendChild(new DOMElement('dcterms:title', 'The Youth\'s History of the United States, etc', self::$NS['dcterms']));
        $content->appendChild(new DOMElement('dcterms:author', 'Ellis, Edward Sylvester', self::$NS['dcterms']));
        $content->appendChild(new DOMElement('dcterms:identifier', '001059471', self::$NS['dcterms']));
        $content->appendChild(new DOMElement('dcterms:identifier', 'British Library HMNTS 9605.f.8.', self::$NS['dcterms']));
        $content->appendChild(new DOMElement('dcterms:publisher', 'Cassell &amp; Co.', self::$NS['dcterms']));
        
        $client = static::createClient();
        $crawler = $client->request('POST', '/api/sword/2.0/col-iri/1', array(), array(), array(), $doc->saveXML());
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertTrue($response->headers->has('Location'));
        $this->assertStringEndsWith($uuid . '/edit', $response->headers->get('location'));
    }
    
    //6.3.3. Creating a Resource with an Atom Entry
    public function testCreateMultipleResources() {
        $uuid = Uuid::v4();
        
        $doc = new DOMDocument("1.0", "UTF-8");
        $entry = $doc->appendChild($doc->createElementNS(self::$NS['atom'], 'entry'));
        foreach(self::$NS as $k => $v) {
            $ns = $entry->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:' . $k, $v);
        }
        
        $entry->appendChild(new DOMElement('title', 'Image samples from the internet'));
        $entry->appendChild(new DOMElement('id', $uuid));
        $entry->appendChild(new DOMElement('updated', date('c')));
        $author = $entry->appendChild(new DOMElement('author'));
        $name = $author->appendChild(new DOMElement('name', 'various'));
        $summary = $entry->appendChild(new DOMElement('summary', 'Image samples from the internet'));
        $summary->setAttribute('type', 'text');

        $content = $entry->appendChild(new DOMElement('lom:content', 'https://farm4.staticflickr.com/3691/11186563486_8796f4f843_o_d.jpg', self::$NS['lom']));
        $content->setAttribute('size', '899922');
        $content->setAttribute('checksumType', 'md5');
        $content->setAttribute('checksumValue', 'ed5697c06b97f95e1221f857a3c08661');

        $content->appendChild(new DOMElement('dcterms:abstract', 'Image taken from page 275 of "The Youth\'s History of the United States, etc"', self::$NS['dcterms']));
        $content->appendChild(new DOMElement('dcterms:title', 'The Youth\'s History of the United States, etc', self::$NS['dcterms']));
        $content->appendChild(new DOMElement('dcterms:author', 'Ellis, Edward Sylvester', self::$NS['dcterms']));
        $content->appendChild(new DOMElement('dcterms:identifier', '001059471', self::$NS['dcterms']));
        $content->appendChild(new DOMElement('dcterms:identifier', 'British Library HMNTS 9605.f.8.', self::$NS['dcterms']));
        $content->appendChild(new DOMElement('dcterms:publisher', 'Cassell &amp; Co.', self::$NS['dcterms']));
        
        $content = $entry->appendChild(new DOMElement('lom:content', 'http://www.ibiblio.org/wm/paint/auth/monet/parliament/parliament.jpg', self::$NS['lom']));
        $content->setAttribute('size', '198423');
        $content->setAttribute('checksumType', 'md5');
        $content->setAttribute('checksumValue', '5619bbabea01c0841cd99c6cf4ad3b33');

        $content->appendChild(new DOMElement('dcterms:title', 'Houses of Parliament, London, Sun Breaking Through the Fog ', self::$NS['dcterms']));
        $content->appendChild(new DOMElement('dcterms:creator', 'Monet, Claude', self::$NS['dcterms']));
        
        $content = $entry->appendChild(new DOMElement('lom:content', 'http://www.ibiblio.org/wm/paint/auth/gauguin/gauguin.alyscamps.jpg', self::$NS['lom']));
        $content->setAttribute('size', '176098');
        $content->setAttribute('checksumType', 'md5');
        $content->setAttribute('checksumValue', 'ff9a208611f892d147ef0f213150323e');

        $content->appendChild(new DOMElement('dcterms:title', 'Les Alyscamps, Arles', self::$NS['dcterms']));
        $content->appendChild(new DOMElement('dcterms:author', 'Gaugin, Paul', self::$NS['dcterms']));
        
        $client = static::createClient();
        $crawler = $client->request('POST', '/api/sword/2.0/col-iri/1', array(), array(), array(), $doc->saveXML());
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertTrue($response->headers->has('Location'));
        $this->assertStringEndsWith($uuid . '/edit', $response->headers->get('location'));
    }
}
