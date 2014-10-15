<?php

namespace LOCKSSOMatic\SWORDBundle\Tests\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use DOMDocument;
use DOMElement;
use DOMXPath;
use J20\Uuid\Uuid;
use SimpleXMLElement;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends WebTestCase
{

    private static $NS = array(
        'dcterms' => "http://purl.org/dc/terms/",
        'sword' => "http://purl.org/net/sword/terms/",
        'atom' => "http://www.w3.org/2005/Atom",
        'lom' => "http://lockssomatic.info/SWORD2",
        'rdf' => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#',
        'app' => "http://www.w3.org/2007/app",
    );

    // MUST CREATE A CONTENT PROVIDER ID FOR THIS TO WORK.
    // AND the content provider must have set the maxUploadSize and uploadChecksumType=md5
    public function testServiceDocument()
    {
        $client = static::createClient();

        $crawler = $client->request(
                'GET', '/api/sword/2.0/sd-iri', array(), array(), array('HTTP_X-On-Behalf-Of' => 1)
        );

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $xml = new SimpleXMLElement($response->getContent());
        foreach (self::$NS as $k => $v) {
            $xml->registerXPathNamespace($k, $v);
        }

        $this->assertEquals('2.0', $xml->children(self::$NS['sword'])->version[0]);
        $this->assertGreaterThan(1, $xml->children(self::$NS['sword'])->maxUploadSize[0]);
        $this->assertEquals('md5', $xml->children(self::$NS['lom'])->uploadChecksumType[0]);

        foreach ($xml->children(self::$NS['app'])->collection as $coll) {
            $this->assertStringEndsWith('/api/sword/2.0/col-iri/1', $coll['href']);
        }

        $tmp = $xml->xpath('//app:accept/text()');
        $accept = $tmp[0];
        $this->assertTrue(strlen($accept) > 0);
    }

    /**
     * On-Behalf-Of header is required - should get an empty response without it.
     */
    public function testServiceDocumentNoBehalfOf()
    {
        $client = static::createClient();

        $crawler = $client->request(
                'GET', '/api/sword/2.0/sd-iri'
        );

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_PRECONDITION_FAILED, $response->getStatusCode());
        $content = $response->getContent();
        $this->assertTrue($content === NULL || $content === '');
    }

    /**
     * On-Behalf-Of header should be a number. 
     */
    public function testServiceDocumentStringOnBehalfOf()
    {
        $client = static::createClient();

        $crawler = $client->request(
                'GET', '/api/sword/2.0/sd-iri', array(), array(), array('HTTP_X-On-Behalf-Of' => 'Magneto')
        );

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_PRECONDITION_FAILED, $response->getStatusCode());
        $content = $response->getContent();
        $this->assertTrue($content === NULL || $content === '');
    }

    /**
     * On-Behalf-Of must match a content provider
     */
    public function testServiceDocumentBadOnBehalfOf()
    {
        $client = static::createClient();

        $crawler = $client->request(
                'GET', '/api/sword/2.0/sd-iri', array(), array(), array('HTTP_X-On-Behalf-Of' => 297845)
        );

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
        $content = $response->getContent();
        $this->assertTrue($content === NULL || $content === '');
    }

    //6.3.3. Creating a Resource with an Atom Entry
    public function testCreateNoResource()
    {
        $uuid = Uuid::v4();

        $xml = new SimpleXMLElement('<entry />');
        foreach (self::$NS as $k => $v) {
            $xml->registerXPathNamespace($k, $v);
        }
        $xml->addAttribute('xmlns', self::$NS['atom']);

        $xml->addChild('title', 'Empty deposit', self::$NS['atom']);
        $xml->addChild('id', $uuid, self::$NS['atom']);
        $xml->addChild('updated', date('c'), self::$NS['atom']);
        $author = $xml->addChild('author', null, self::$NS['atom']);
        $author->addChild('name', 'Me, A Bunny', self::$NS['atom']);

        $summary = $xml->addChild('summary', 'No content', self::$NS['atom']);
        $summary->addAttribute('type', 'text');

        $client = static::createClient();
        $crawler = $client->request(
                'POST', '/api/sword/2.0/col-iri/1', array(), array(), array(), $xml->asXML());

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_PRECONDITION_FAILED, $response->getStatusCode());
        $content = $response->getContent();
        $this->assertTrue($content === NULL || $content === '');

        /** @var EntityManager */
        $em = $client->getContainer()->get('doctrine')->getManager();
        $deposits = $em->getRepository('LOCKSSOMaticCRUDBundle:Deposits')->findBy(array('title' => 'Empty deposit'));
        $this->assertEquals(0, count($deposits));
    }

    //6.3.3. Creating a Resource with an Atom Entry
    public function testCreateSingleResource()
    {
        $client = static::createClient();
        $uuid = Uuid::v4();

        $depositXml = new SimpleXMLElement('<entry />');
        foreach (self::$NS as $k => $v) {
            $depositXml->registerXPathNamespace($k, $v);
        }
        $depositXml->addAttribute('xmlns', self::$NS['atom']);

        $depositXml->addChild('title', 'Single content deposit');
        $depositXml->addChild('id', $uuid);
        $depositXml->addChild('updated', date('c'));
        $author = $depositXml->addChild('author');
        $author->addChild('name', 'Me, A Bunny');

        $summary = $depositXml->addChild('summary', 'One content element');
        $summary->addAttribute('type', 'text');

        $content = $depositXml->addChild('content', 'https://farm4.staticflickr.com/3691/11186563486_8796f4f843_o_d.jpg', self::$NS['lom']);
        $content->addAttribute('size', '899922');
        $content->addAttribute('checksumType', 'md5');
        $content->addAttribute('checksumValue', 'ed5697c06b97f95e1221f857a3c08661');

        $crawler = $client->request(
                'POST', '/api/sword/2.0/col-iri/1', array(), array(), array(), $depositXml->asXML()
        );
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertTrue($response->headers->has('Location'));
        $this->assertStringEndsWith($uuid . '/edit', $response->headers->get('location'));

        $responseXml = new SimpleXMLElement($response->getContent());
        foreach (self::$NS as $k => $v) {
            $responseXml->registerXPathNamespace($k, $v);
        }

        $this->assertEquals(1, count($responseXml->xpath('atom:link[@rel="edit"]')));
        $this->assertGreaterThan(0, count($responseXml->xpath('atom:link[@rel="edit-media"]')));
        $this->assertEquals(1, count($responseXml->xpath('atom:link[@rel="http://purl.org/net/sword/terms/add"]')));

        // Follow the location header, which should give the same deposit receipt.
        $oldContent = $response->getContent();

        // get the location, in a URI that the kernel can understand. Full, absolute URIs 
        // will throw 404 errors.
        // 
        //Symfony simulates an http client and tests against an instance of the 
        //Kernel created for that test. There are no web servers involved.
        $location = preg_replace('/^http.*app_dev.php/', '', $response->headers->get('location'));

        $crawler = $client->request('GET', $location);
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $recieptXml = new SimpleXMLElement($response->getContent());
        foreach (self::$NS as $k => $v) {
            $recieptXml->registerXPathNamespace($k, $v);
        }

        $this->assertEquals(1, count($recieptXml->xpath('atom:link[@rel="edit"]')));
        $this->assertGreaterThan(0, count($recieptXml->xpath('atom:link[@rel="edit-media"]')));
        $this->assertEquals(1, count($recieptXml->xpath('atom:link[@rel="http://purl.org/net/sword/terms/add"]')));
    }

    //6.3.3. Creating a Resource with an Atom Entry
    public function testCreateMultipleResources()
    {
        $uuid = Uuid::v4();
        $depositXml = new SimpleXMLElement('<entry />');
        foreach (self::$NS as $k => $v) {
            $depositXml->registerXPathNamespace($k, $v);
        }
        $depositXml->addAttribute('xmlns', self::$NS['atom']);

        $depositXml->addChild('title', 'Single content deposit');
        $depositXml->addChild('id', $uuid);
        $depositXml->addChild('updated', date('c'));
        $author = $depositXml->addChild('author');
        $author->addChild('name', 'Me, A Bunny');

        $summary = $depositXml->addChild('summary', 'One content element');
        $summary->addAttribute('type', 'text');

        $content = $depositXml->addChild('content', 'https://farm4.staticflickr.com/3691/11186563486_8796f4f843_o_d.jpg', self::$NS['lom']);
        $content->addAttribute('size', '899922');
        $content->addAttribute('checksumType', 'md5');
        $content->addAttribute('checksumValue', 'ed5697c06b97f95e1221f857a3c08661');

        $content = $depositXml->addChild('content', 'http://www.ibiblio.org/wm/paint/auth/monet/parliament/parliament.jpg', self::$NS['lom']);
        $content->addAttribute('size', '198423');
        $content->addAttribute('checksumType', 'md5');
        $content->addAttribute('checksumValue', '5619bbabea01c0841cd99c6cf4ad3b33');

        $content = $depositXml->addChild('content', 'http://www.ibiblio.org/wm/paint/auth/gauguin/gauguin.alyscamps.jpg', self::$NS['lom']);
        $content->addAttribute('size', '176098');
        $content->addAttribute('checksumType', 'md5');
        $content->addAttribute('checksumValue', 'ff9a208611f892d147ef0f213150323e');

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/sword/2.0/col-iri/1', array(), array(), array(), $depositXml->asXML());
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertTrue($response->headers->has('Location'));
        $this->assertStringEndsWith($uuid . '/edit', $response->headers->get('location'));

        $responseXml = new SimpleXMLElement($response->getContent());
        foreach (self::$NS as $k => $v) {
            $responseXml->registerXPathNamespace($k, $v);
        }

        $this->assertEquals(1, count($responseXml->xpath('atom:link[@rel="edit"]')));
        $this->assertGreaterThan(0, count($responseXml->xpath('atom:link[@rel="edit-media"]')));
        $this->assertEquals(1, count($responseXml->xpath('atom:link[@rel="http://purl.org/net/sword/terms/add"]')));
    }

    public function testSwordStatement()
    {
        $uuid = Uuid::v4();
        $depositXml = new SimpleXMLElement('<entry />');
        foreach (self::$NS as $k => $v) {
            $depositXml->registerXPathNamespace($k, $v);
        }
        $depositXml->addAttribute('xmlns', self::$NS['atom']);

        $depositXml->addChild('title', 'Single content deposit');
        $depositXml->addChild('id', $uuid);
        $depositXml->addChild('updated', date('c'));
        $author = $depositXml->addChild('author');
        $author->addChild('name', 'Me, A Bunny');

        $summary = $depositXml->addChild('summary', 'One content element');
        $summary->addAttribute('type', 'text');

        $content = $depositXml->addChild('content', 'https://farm4.staticflickr.com/3691/11186563486_8796f4f843_o_d.jpg', self::$NS['lom']);
        $content->addAttribute('size', '899922');
        $content->addAttribute('checksumType', 'md5');
        $content->addAttribute('checksumValue', 'ed5697c06b97f95e1221f857a3c08661');
        
        $client = static::createClient();
        $crawler = $client->request('POST', '/api/sword/2.0/col-iri/1', array(), array(), array(), $depositXml->asXML());
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $responseXml = new SimpleXMLElement($response->getContent());
        foreach (self::$NS as $k => $v) {
            $responseXml->registerXPathNamespace($k, $v);
        }
        
        $tmp = $responseXml->xpath('//atom:link[@rel="http://purl.org/net/sword/terms/statement"]/@href');
        $stateIri = $tmp[0];
        $location = preg_replace('/^http.*app_dev.php/', '', $stateIri);
        
        $crawler = $client->request('GET', $location);
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        
        $client->getContainer()->get('monolog.logger.sword')->log('error', $response->getContent());
        // @TODO finish testing this - right now everything is stubbed out.
    }

}
