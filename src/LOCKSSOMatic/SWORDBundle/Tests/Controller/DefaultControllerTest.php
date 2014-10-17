<?php

namespace LOCKSSOMatic\SWORDBundle\Tests\Controller;

use Doctrine\ORM\EntityManager;
use J20\Uuid\Uuid;
use LOCKSSOMatic\CRUDBundle\Entity\Deposits;
use LOCKSSOMatic\SWORDBundle\Utilities\Namespaces;
use SimpleXMLElement;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends WebTestCase
{

    /**
     *
     * @var Namespaces
     */
    private $namespaces;

    public function __construct()
    {
        parent::__construct();
        $this->namespaces = new Namespaces();
    }

    /**
     * Get a SimpleXMLElement from a string, and assign the necessary
     * xpath namespaces.
     *
     * @param string $string
     * @return SimpleXMLElement
     */
    private function getSimpleXML($string)
    {
        $xml = new SimpleXMLElement($string);
        $this->namespaces->registerNamespaces($xml);
        return $xml;
    }

    private function createDepositXML($uuid, $title = 'Untitled deposit')
    {
        $xml = new SimpleXMLElement('<entry />');
        $this->namespaces->registerNamespaces($xml);
        $xml->addAttribute('xmlns', Namespaces::ATOM);

        $xml->addChild('title', $title, Namespaces::ATOM);
        $xml->addChild('id', $uuid, Namespaces::ATOM);
        $xml->addChild('updated', date('c'), Namespaces::ATOM);
        $author = $xml->addChild('author', null, Namespaces::ATOM);
        $author->addChild('name', 'Me, A Bunny', Namespaces::ATOM);

        $summary = $xml->addChild('summary', 'No content', Namespaces::ATOM);
        $summary->addAttribute('type', 'text');
        return $xml;
    }

    private function addContentItem($xml, $url, $size, $csType, $csValue)
    {
        $content = $xml->addChild('content', $url, Namespaces::LOM);
        $content->addAttribute('size', $size);
        $content->addAttribute('checksumType', $csType);
        $content->addAttribute('checksumValue', $csValue);
    }

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
        $this->assertRegexp('/text\/xml/', $response->headers->get('Content-type'));

        $xml = $this->getSimpleXML($response->getContent());

        $this->assertEquals('2.0', $xml->children(Namespaces::SWORD)->version[0]);
        $this->assertGreaterThan(1, $xml->children(Namespaces::SWORD)->maxUploadSize[0]);
        $this->assertEquals('md5', $xml->children(Namespaces::LOM)->uploadChecksumType[0]);

        foreach ($xml->children(Namespaces::APP)->collection as $coll) {
            $this->assertStringEndsWith('/api/sword/2.0/col-iri/1', $coll['href']);
        }

        $tmp = $xml->xpath('//app:accept/text()');
        $this->assertTrue(strlen($tmp[0]) > 0);
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
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertStringStartsWith('text/xml', $response->headers->get('Content-type'));

        $responseXml = $this->getSimpleXML($response->getContent());
        $this->assertEquals('error', $responseXml->getName());
        $this->assertEquals('http://purl.org/net/sword/error/ErrorBadRequest', $responseXml['href']);
        $this->assertNotNull($responseXml->children(Namespaces::ATOM)->summary[0]);
        $this->assertNotNull($responseXml->children(Namespaces::SWORD)->verboseDescription[0]);
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
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
        $responseXml = $this->getSimpleXML($response->getContent());
        $this->assertEquals('error', $responseXml->getName());
        $this->assertEquals('http://purl.org/net/sword/error/TargetOwnerUnknown', $responseXml['href']);
        $this->assertNotNull($responseXml->children(Namespaces::ATOM)->summary[0]);
        $this->assertNotNull($responseXml->children(Namespaces::SWORD)->verboseDescription[0]);
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
        $responseXml = $this->getSimpleXML($response->getContent());
        $this->assertEquals('error', $responseXml->getName());
        $this->assertEquals('http://purl.org/net/sword/error/TargetOwnerUnknown', $responseXml['href']);
        $this->assertNotNull($responseXml->children(Namespaces::ATOM)->summary[0]);
        $this->assertNotNull($responseXml->children(Namespaces::SWORD)->verboseDescription[0]);
    }
     
    public function testCreateBadProvider()
    {
        $uuid = Uuid::v4();
       $xml = $this->createDepositXML($uuid, 'Empty deposit');
        $client = static::createClient();
        $crawler = $client->request(
            'POST', '/api/sword/2.0/col-iri/27', array(), array(), array(), $xml->asXML()
        );
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
        $content = $response->getContent();
        $responseXml = $this->getSimpleXML($content);
        $this->assertEquals('error', $responseXml->getName());
        $this->assertEquals('http://purl.org/net/sword/error/TargetOwnerUnknown', $responseXml['href']);
        $this->assertNotNull($responseXml->children(Namespaces::ATOM)->summary[0]);
        $this->assertNotNull($responseXml->children(Namespaces::SWORD)->verboseDescription[0]);
    }

    //6.3.3. Creating a Resource with an Atom Entry
    public function testCreateNoResource()
    {
        $uuid = Uuid::v4();

        $xml = $this->createDepositXML($uuid, 'Empty deposit');

        $client = static::createClient();
        $crawler = $client->request(
            'POST', '/api/sword/2.0/col-iri/1', array(), array(), array(), $xml->asXML()
        );

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $content = $response->getContent();
        $responseXml = $this->getSimpleXML($content);
        $this->assertEquals('error', $responseXml->getName());
        $this->assertEquals('http://purl.org/net/sword/error/ErrorBadRequest', $responseXml['href']);
        $this->assertNotNull($responseXml->children(Namespaces::ATOM)->summary[0]);
        $this->assertNotNull($responseXml->children(Namespaces::SWORD)->verboseDescription[0]);

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

        $depositXml = $this->createDepositXML($uuid);
        $this->addContentItem(
            $depositXml, 'https://farm4.staticflickr.com/3691/11186563486_8796f4f843_o_d.jpg', 899922, 'md5', 'ed5697c06b97f95e1221f857a3c08661'
        );

        $crawler = $client->request(
            'POST', '/api/sword/2.0/col-iri/1', array(), array(), array(), $depositXml->asXML()
        );
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertTrue($response->headers->has('Location'));
        $this->assertStringEndsWith($uuid . '/edit', $response->headers->get('location'));

        $responseXml = $this->getSimpleXML($response->getContent());

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

        $recieptXml = $this->getSimpleXML($response->getContent());

        $this->assertEquals(1, count($recieptXml->xpath('atom:link[@rel="edit"]')));
        $this->assertGreaterThan(0, count($recieptXml->xpath('atom:link[@rel="edit-media"]')));
        $this->assertEquals(1, count($recieptXml->xpath('atom:link[@rel="http://purl.org/net/sword/terms/add"]')));
    }

    //6.3.3. Creating a Resource with an Atom Entry
    public function testCreateMultipleResources()
    {
        $uuid = Uuid::v4();
        $depositXml = $this->createDepositXML($uuid);
        $this->addContentItem(
            $depositXml, 'https://farm4.staticflickr.com/3691/11186563486_8796f4f843_o_d.jpg', 899922, 'md5', 'ed5697c06b97f95e1221f857a3c08661'
        );

        $this->addContentItem(
            $depositXml, 'http://www.ibiblio.org/wm/paint/auth/monet/parliament/parliament.jpg', 899922, 'md5', 'ed5697c06b97f95e1221f857a3c08661'
        );

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/sword/2.0/col-iri/1', array(), array(), array(), $depositXml->asXML());
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertTrue($response->headers->has('Location'));
        $this->assertStringEndsWith($uuid . '/edit', $response->headers->get('location'));

        $responseXml = $this->getSimpleXML($response->getContent());

        $this->assertEquals(1, count($responseXml->xpath('atom:link[@rel="edit"]')));
        $this->assertGreaterThan(0, count($responseXml->xpath('atom:link[@rel="edit-media"]')));
        $this->assertEquals(1, count($responseXml->xpath('atom:link[@rel="http://purl.org/net/sword/terms/add"]')));
    }
    
    // fetch a deposit receipt
    public function testDepositReceiptAction()
    {
        $uuid = Uuid::v4();
        $depositXml = $this->createDepositXML($uuid);
        $this->addContentItem(
            $depositXml, 'https://farm4.staticflickr.com/3691/11186563486_8796f4f843_o_d.jpg', 899922, 'md5', 'ed5697c06b97f95e1221f857a3c08661'
        );
        $client = static::createClient();
        $client->request('POST', '/api/sword/2.0/col-iri/1', array(), array(), array(), $depositXml->asXML());
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        
        $crawler = $client->request('GET', '/api/sword/2.0/cont-iri/1/' . $uuid . '/edit');
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $recieptXml = $this->getSimpleXML($response->getContent());

        $this->assertEquals(1, count($recieptXml->xpath('atom:link[@rel="edit"]')));
        $this->assertGreaterThan(0, count($recieptXml->xpath('atom:link[@rel="edit-media"]')));
        $this->assertEquals(1, count($recieptXml->xpath('atom:link[@rel="http://purl.org/net/sword/terms/add"]')));
    }
    
    // fetch a deposit receipt with a bad collectionId.
    public function testDepositReceiptBadCollection()
    {
        $uuid = Uuid::v4();
        $depositXml = $this->createDepositXML($uuid);
        $this->addContentItem(
            $depositXml, 'https://farm4.staticflickr.com/3691/11186563486_8796f4f843_o_d.jpg', 899922, 'md5', 'ed5697c06b97f95e1221f857a3c08661'
        );
        $client = static::createClient();
        $client->request('POST', '/api/sword/2.0/col-iri/1', array(), array(), array(), $depositXml->asXML());
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        
        $crawler = $client->request('GET', '/api/sword/2.0/cont-iri/2/' . $uuid . '/edit');
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
        
        $responseXml = $this->getSimpleXML($response->getContent());
        $this->assertEquals('error', $responseXml->getName());
        $this->assertEquals('http://purl.org/net/sword/error/TargetOwnerUnknown', $responseXml['href']);
        $this->assertNotNull($responseXml->children(Namespaces::ATOM)->summary[0]);
        $this->assertNotNull($responseXml->children(Namespaces::SWORD)->verboseDescription[0]);
    }
    
    // fetch a deposit receipt with a bad uuid.
    public function testDepositReceiptBadId()
    {
        $uuid = Uuid::v4();

        $client = static::createClient();
        
        $crawler = $client->request('GET', '/api/sword/2.0/cont-iri/1/' . $uuid . '/edit');
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $responseXml = $this->getSimpleXML($response->getContent());
        $this->assertEquals('error', $responseXml->getName());
        $this->assertEquals('http://purl.org/net/sword/error/ErrorBadRequest', $responseXml['href']);
        $this->assertNotNull($responseXml->children(Namespaces::ATOM)->summary[0]);
        $this->assertNotNull($responseXml->children(Namespaces::SWORD)->verboseDescription[0]);
    }
    
    public function testViewDepositAction()
    {
        $uuid = Uuid::v4();
        $depositXml = $this->createDepositXML($uuid);
        $this->addContentItem(
            $depositXml, 'https://farm4.staticflickr.com/3691/11186563486_8796f4f843_o_d.jpg', 899922, 'md5', 'ed5697c06b97f95e1221f857a3c08661'
        );
        $client = static::createClient();
        $crawler = $client->request('POST', '/api/sword/2.0/col-iri/1', array(), array(), array(), $depositXml->asXML());
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertTrue($response->headers->has('Location'));
        $this->assertStringEndsWith($uuid . '/edit', $response->headers->get('location'));

        $crawler = $client->request('GET', '/api/sword/2.0/cont-iri/1/' . $uuid);
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseXml = $this->getSimpleXML($response->getContent());
        $this->assertEquals(1, count($responseXml->xpath('//lom:content')));
    }
    
    // fetch a deposit receipt with a bad collectionId.
    public function testViewDepositBadCollection()
    {
        $uuid = Uuid::v4();
        $depositXml = $this->createDepositXML($uuid);
        $this->addContentItem(
            $depositXml, 'https://farm4.staticflickr.com/3691/11186563486_8796f4f843_o_d.jpg', 899922, 'md5', 'ed5697c06b97f95e1221f857a3c08661'
        );
        $client = static::createClient();
        $client->request('POST', '/api/sword/2.0/col-iri/1', array(), array(), array(), $depositXml->asXML());
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        
        $crawler = $client->request('GET', '/api/sword/2.0/cont-iri/2/' . $uuid);
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
        
        $responseXml = $this->getSimpleXML($response->getContent());
        $this->assertEquals('error', $responseXml->getName());
        $this->assertEquals('http://purl.org/net/sword/error/TargetOwnerUnknown', $responseXml['href']);
        $this->assertNotNull($responseXml->children(Namespaces::ATOM)->summary[0]);
        $this->assertNotNull($responseXml->children(Namespaces::SWORD)->verboseDescription[0]);
    }
    
    // fetch a deposit receipt with a bad uuid.
    public function testViewDepositBadId()
    {
        $uuid = Uuid::v4();

        $client = static::createClient();
        
        $crawler = $client->request('GET', '/api/sword/2.0/cont-iri/1/' . $uuid);
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $responseXml = $this->getSimpleXML($response->getContent());
        $this->assertEquals('error', $responseXml->getName());
        $this->assertEquals('http://purl.org/net/sword/error/ErrorBadRequest', $responseXml['href']);
        $this->assertNotNull($responseXml->children(Namespaces::ATOM)->summary[0]);
        $this->assertNotNull($responseXml->children(Namespaces::SWORD)->verboseDescription[0]);
    }
    
    public function testEditDepositAction()
    {
        $uuid = Uuid::v4();
        $depositXml = $this->createDepositXML($uuid);
        $url = 'https://farm4.staticflickr.com/3691/11186563486_8796f4f843_o_d.jpg';
        $this->addContentItem(
            $depositXml, $url, 899922, 'md5', 'ed5697c06b97f95e1221f857a3c08661'
        );
        $client = static::createClient();
        $crawler = $client->request('POST', '/api/sword/2.0/col-iri/1', array(), array(), array(), $depositXml->asXML());
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertTrue($response->headers->has('Location'));
        $this->assertStringEndsWith($uuid . '/edit', $response->headers->get('location'));

        $crawler = $client->request('GET', '/api/sword/2.0/cont-iri/1/' . $uuid);
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseXml = $this->getSimpleXML($response->getContent());
        $this->assertEquals(1, count($responseXml->xpath('//lom:content')));
        
        $responseXml->children(Namespaces::LOM)->content[0]->recrawl = 'false';
        
        $crawler = $client->request(
            'PUT',
            '/api/sword/2.0/cont-iri/1/' . $uuid . '/edit',
            array(),
            array(),
            array(),
            $responseXml->asXML()
        );
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        
        /** @var EntityManager */
        $em = $client->getContainer()->get('doctrine')->getManager();
        
        $deposit = $em->getRepository('LOCKSSOMaticCRUDBundle:Deposits')->findOneBy(
            array('uuid' => $uuid)
        );
        $content = $em->getRepository('LOCKSSOMaticCRUDBundle:Content')->findOneBy(
            array(
                'url' => $url,
                'deposit' => $deposit,
            )
        );
        $this->assertFalse($content->getRecrawl());
    }

    public function testEditDepositBadCollection()
    {
        $uuid = Uuid::v4();
        $depositXml = $this->createDepositXML($uuid);
        $url = 'https://farm4.staticflickr.com/3691/11186563486_8796f4f843_o_d.jpg';
        $this->addContentItem(
            $depositXml, $url, 899922, 'md5', 'ed5697c06b97f95e1221f857a3c08661'
        );
        $client = static::createClient();
        $crawler = $client->request('POST', '/api/sword/2.0/col-iri/1', array(), array(), array(), $depositXml->asXML());
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertTrue($response->headers->has('Location'));
        $this->assertStringEndsWith($uuid . '/edit', $response->headers->get('location'));

        $crawler = $client->request('GET', '/api/sword/2.0/cont-iri/1/' . $uuid);
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseXml = $this->getSimpleXML($response->getContent());
        $this->assertEquals(1, count($responseXml->xpath('//lom:content')));
        
        $responseXml->children(Namespaces::LOM)->content[0]->recrawl = 'false';
        
        $crawler = $client->request(
            'PUT',
            '/api/sword/2.0/cont-iri/2/' . $uuid . '/edit',
            array(),
            array(),
            array(),
            $responseXml->asXML()
        );
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
        
        $responseXml = $this->getSimpleXML($response->getContent());
        $this->assertEquals('error', $responseXml->getName());
        $this->assertEquals('http://purl.org/net/sword/error/TargetOwnerUnknown', $responseXml['href']);
        $this->assertNotNull($responseXml->children(Namespaces::ATOM)->summary[0]);
        $this->assertNotNull($responseXml->children(Namespaces::SWORD)->verboseDescription[0]);
        
        /** @var EntityManager */
        $em = $client->getContainer()->get('doctrine')->getManager();
        
        $deposit = $em->getRepository('LOCKSSOMaticCRUDBundle:Deposits')->findOneBy(
            array('uuid' => $uuid)
        );
        $content = $em->getRepository('LOCKSSOMaticCRUDBundle:Content')->findOneBy(
            array(
                'url' => $url,
                'deposit' => $deposit,
            )
        );
        $this->assertTrue($content->getRecrawl() === 1 || $content->getRecrawl());
    }

    public function testEditDepositBadId()
    {
        $uuid = Uuid::v4();
        $depositXml = $this->createDepositXML($uuid);
        $url = 'https://farm4.staticflickr.com/3691/11186563486_8796f4f843_o_d.jpg';
        $this->addContentItem(
            $depositXml, $url, 899922, 'md5', 'ed5697c06b97f95e1221f857a3c08661'
        );
        $client = static::createClient();
        $crawler = $client->request('POST', '/api/sword/2.0/col-iri/1', array(), array(), array(), $depositXml->asXML());
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertTrue($response->headers->has('Location'));
        $this->assertStringEndsWith($uuid . '/edit', $response->headers->get('location'));

        $crawler = $client->request('GET', '/api/sword/2.0/cont-iri/1/' . $uuid);
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseXml = $this->getSimpleXML($response->getContent());
        $this->assertEquals(1, count($responseXml->xpath('//lom:content')));
        
        $responseXml->children(Namespaces::LOM)->content[0]->recrawl = 'false';
        
        $crawler = $client->request(
            'PUT',
            '/api/sword/2.0/cont-iri/1/' . Uuid::v4() . '/edit',
            array(),
            array(),
            array(),
            $responseXml->asXML()
        );
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        
        $responseXml = $this->getSimpleXML($response->getContent());
        $this->assertEquals('error', $responseXml->getName());
        $this->assertEquals('http://purl.org/net/sword/error/ErrorBadRequest', $responseXml['href']);
        $this->assertNotNull($responseXml->children(Namespaces::ATOM)->summary[0]);
        $this->assertNotNull($responseXml->children(Namespaces::SWORD)->verboseDescription[0]);
        
        /** @var EntityManager */
        $em = $client->getContainer()->get('doctrine')->getManager();
        
        $deposit = $em->getRepository('LOCKSSOMaticCRUDBundle:Deposits')->findOneBy(
            array('uuid' => $uuid)
        );
        $content = $em->getRepository('LOCKSSOMaticCRUDBundle:Content')->findOneBy(
            array(
                'url' => $url,
                'deposit' => $deposit,
            )
        );
        $this->assertTrue($content->getRecrawl() === 1 || $content->getRecrawl());
    }
    
    public function testEditDepositNoContent()
    {
        $uuid = Uuid::v4();
        $depositXml = $this->createDepositXML($uuid);
        $url = 'https://farm4.staticflickr.com/3691/11186563486_8796f4f843_o_d.jpg';
        $this->addContentItem(
            $depositXml, $url, 899922, 'md5', 'ed5697c06b97f95e1221f857a3c08661'
        );
        $client = static::createClient();
        $crawler = $client->request('POST', '/api/sword/2.0/col-iri/1', array(), array(), array(), $depositXml->asXML());
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertTrue($response->headers->has('Location'));
        $this->assertStringEndsWith($uuid . '/edit', $response->headers->get('location'));

        $crawler = $client->request('GET', '/api/sword/2.0/cont-iri/1/' . $uuid);
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseXml = $this->getSimpleXML($response->getContent());
        $this->assertEquals(1, count($responseXml->xpath('//lom:content')));
        
        unset($responseXml->children(Namespaces::LOM)->content);
        
        $crawler = $client->request(
            'PUT',
            '/api/sword/2.0/cont-iri/1/' . Uuid::v4() . '/edit',
            array(),
            array(),
            array(),
            $responseXml->asXML()
        );
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        
        $responseXml = $this->getSimpleXML($response->getContent());
        $this->assertEquals('error', $responseXml->getName());
        $this->assertEquals('http://purl.org/net/sword/error/ErrorBadRequest', $responseXml['href']);
        $this->assertNotNull($responseXml->children(Namespaces::ATOM)->summary[0]);
        $this->assertNotNull($responseXml->children(Namespaces::SWORD)->verboseDescription[0]);
        
        /** @var EntityManager */
        $em = $client->getContainer()->get('doctrine')->getManager();
        
        $deposit = $em->getRepository('LOCKSSOMaticCRUDBundle:Deposits')->findOneBy(
            array('uuid' => $uuid)
        );
        $content = $em->getRepository('LOCKSSOMaticCRUDBundle:Content')->findOneBy(
            array(
                'url' => $url,
                'deposit' => $deposit,
            )
        );
        $this->assertTrue($content->getRecrawl() === 1 || $content->getRecrawl());
    }
    
    public function testEditDepositNoContentUpdated()
    {
        $uuid = Uuid::v4();
        $depositXml = $this->createDepositXML($uuid);
        $url = 'https://farm4.staticflickr.com/3691/11186563486_8796f4f843_o_d.jpg';
        $this->addContentItem(
            $depositXml, $url, 899922, 'md5', 'ed5697c06b97f95e1221f857a3c08661'
        );
        $client = static::createClient();
        $crawler = $client->request('POST', '/api/sword/2.0/col-iri/1', array(), array(), array(), $depositXml->asXML());
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertTrue($response->headers->has('Location'));
        $this->assertStringEndsWith($uuid . '/edit', $response->headers->get('location'));

        $crawler = $client->request('GET', '/api/sword/2.0/cont-iri/1/' . $uuid);
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseXml = $this->getSimpleXML($response->getContent());
        $this->assertEquals(1, count($responseXml->xpath('//lom:content')));
        
        unset($responseXml->children(Namespaces::LOM)->content);
        $responseXml->addChild('content', 'http://example.com/', Namespaces::LOM);
            
        $crawler = $client->request(
            'PUT',
            '/api/sword/2.0/cont-iri/1/' . $uuid . '/edit',
            array(),
            array(),
            array(),
            $responseXml->asXML()
        );
        $response = $client->getResponse();
        print $response->getContent();
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }
    
    // @TODO finish this test, once the swordStatementAction is
    // finished.
    public function testSwordStatement()
    {
        $uuid = Uuid::v4();
        $depositXml = $this->createDepositXML($uuid);
        $this->addContentItem(
            $depositXml, 'https://farm4.staticflickr.com/3691/11186563486_8796f4f843_o_d.jpg', 899922, 'md5', 'ed5697c06b97f95e1221f857a3c08661'
        );

        $client = static::createClient();
        $crawler = $client->request(
            'POST', '/api/sword/2.0/col-iri/1', array(), array(), array(), $depositXml->asXML()
        );
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $responseXml = $this->getSimpleXML($response->getContent());

        $tmp = $responseXml->xpath('//atom:link[@rel="http://purl.org/net/sword/terms/statement"]/@href');
        $stateIri = $tmp[0];
        $location = preg_replace('/^http.*app_dev.php/', '', $stateIri);

        $crawler = $client->request('GET', $location);
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        //$client->getContainer()->get('monolog.logger.sword')->log('error', 'uri: ' . $location);
        // @TODO finish testing this - right now everything is stubbed out.
    }
}
