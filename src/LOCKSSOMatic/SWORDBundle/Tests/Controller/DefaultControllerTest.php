<?php

/*
 * The MIT License
 *
 * Copyright (c) 2014 Mark Jordan, mjordan@sfu.ca.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace LOCKSSOMatic\SWORDBundle\Tests\Controller;

use Doctrine\ORM\EntityManager;
use J20\Uuid\Uuid;
use LOCKSSOMatic\CRUDBundle\Entity\Content;
use LOCKSSOMatic\CRUDBundle\Entity\ContentProviders;
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

    /**
     * @var EntityManager
     */
    private static $em;
    
    private static $logger;

    /**
     * @var ContentProviders
     */
    private static $provider;

    public function __construct()
    {
        parent::__construct();
        static::bootKernel();

        $this->namespaces = new Namespaces();
        static::$em = static::$kernel->getContainer()->get('doctrine')->getManager();
        static::$logger = static::$kernel->getContainer()->get('logger');
    }

    // called before the the class is run.
    public static function setUpBeforeClass()
    {
        $provider = new ContentProviders();
        $provider->setType('test');
        $provider->setName('Test provider 1');
        $provider->setIpAddress('127.0.0.1');
        $provider->setHostname('provider.example.com');
        $provider->setChecksumType('md5');
        $provider->setMaxFileSize('8000'); // in kB
        $provider->setMaxAuSize('10000'); // also in kB
        $provider->setPermissionUrl('http://provider.example.com/path/to/permissions');
        static::$em->persist($provider);
        static::$em->flush();
        static::$provider = $provider;
    }

    public static function tearDownAfterClass()
    {
        $em = static::$em;
        $em->refresh(self::$provider);
        foreach(static::$provider->getDeposits() as $deposit) {
            foreach($deposit->getContent() as $content) {
                $em->remove($content);
            }
            $em->remove($deposit);
        }
        foreach(static::$provider->getAus() as $au) {
            $em->remove($au);
        }
        static::$em->remove(static::$provider);
        static::$em->flush();
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

    private function addContentItem($xml, $url, $size = null, $csType = null, $csValue = null)
    {
        $content = $xml->addChild('content', $url, Namespaces::LOM);
        
        if ($size !== null) {
            $content->addAttribute('size', $size);
        }
        
        if ($csType !== null) {
            $content->addAttribute('checksumType', $csType);
        }
        
        if ($csValue !== null) {
            $content->addAttribute('checksumValue', $csValue);
        }
    }
    
    private function postDeposit($xml, $uuid)
    {
        $client = static::createClient();
        $client->request(
            'POST', 
            '/api/sword/2.0/col-iri/' . $uuid, 
            array(), 
            array(), 
            array(), 
            $xml->asXML()
        );
        return $client;
    }    

    public function testServiceDocument()
    {
        $client = static::createClient();

        $crawler = $client->request(
            'GET', '/api/sword/2.0/sd-iri', array(), array(), array('HTTP_X-On-Behalf-Of' => self::$provider->getUuid())
        );

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertRegexp('/text\/xml/', $response->headers->get('Content-type'));

        $xml = $this->getSimpleXML($response->getContent());

        $this->assertEquals('2.0', $xml->children(Namespaces::SWORD)->version[0]);
        $this->assertGreaterThan(1, $xml->children(Namespaces::SWORD)->maxUploadSize[0]);
        $this->assertEquals('md5', $xml->children(Namespaces::LOM)->uploadChecksumType[0]);

        foreach ($xml->xpath('//app:collection') as $coll) {
            $this->assertStringEndsWith('/api/sword/2.0/col-iri/' . self::$provider->getUuid(), (string) $coll['href']);
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
    }

    /**
     * On-Behalf-Of must match a content provider
     */
    public function testServiceDocumentBadOnBehalfOf()
    {
        $client = static::createClient();

        $client->request(
            'GET', '/api/sword/2.0/sd-iri', array(), array(), array('HTTP_X-On-Behalf-Of' => 297845)
        );

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testCreateBadProvider()
    {
        $uuid = Uuid::v4();
        $xml = $this->createDepositXML($uuid);
        $client = $this->postDeposit($xml, Uuid::v4());

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());

        $deposits = self::$em->getRepository('LOCKSSOMaticCRUDBundle:Deposits')->findOneBy(array('uuid' => $uuid));
        $this->assertNull($deposits);
    }

    //6.3.3. Creating a Resource with an Atom Entry
    public function testCreateNoResource()
    {
        $uuid = Uuid::v4();
        $depositXml = $this->createDepositXML($uuid);
        $client = $this->postDeposit($depositXml, self::$provider->getUuid());

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $deposits = self::$em->getRepository('LOCKSSOMaticCRUDBundle:Deposits')->findOneBy(array('uuid' => $uuid));
        $this->assertNull($deposits);
    }

    //6.3.3. Creating a Resource with an Atom Entry
    public function testCreateHostMismatch()
    {
        $uuid = Uuid::v4();

        $depositXml = $this->createDepositXML($uuid);
        $this->addContentItem(
            $depositXml,
            'http://notareal.farmanimal.com/3691/11186563486_8796f4f843_o_d.jpg'
        );

        $client = $this->postDeposit($depositXml, self::$provider->getUuid());
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $deposits = self::$em->getRepository('LOCKSSOMaticCRUDBundle:Deposits')->findOneBy(array('uuid' => $uuid));
        $this->assertNull($deposits);
    }

    //6.3.3. Creating a Resource with an Atom Entry
    public function testCreateFileSizeTooLarge()
    {
        $uuid = Uuid::v4();

        $depositXml = $this->createDepositXML($uuid);
        $this->addContentItem(
            $depositXml,
            'http://notareal.farmanimal.com/3691/11186563486_8796f4f843_o_d.jpg',
            811985
        );

        $client = $this->postDeposit($depositXml, self::$provider->getUuid());
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $deposits = self::$em->getRepository('LOCKSSOMaticCRUDBundle:Deposits')->findOneBy(array('uuid' => $uuid));
        $this->assertNull($deposits);
    }

    //6.3.3. Creating a Resource with an Atom Entry
    public function testCreateSingleResource()
    {
        $uuid = Uuid::v4();

        $depositXml = $this->createDepositXML($uuid);
        $this->addContentItem(
            $depositXml,
            'http://provider.example.com/3691/11186563486_8796f4f843_o_d.jpg'
        );

        $client = $this->postDeposit($depositXml, self::$provider->getUuid());
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertTrue($response->headers->has('Location'));
        $this->assertStringEndsWith($uuid . '/edit', $response->headers->get('location'));

        $deposits = self::$em->getRepository('LOCKSSOMaticCRUDBundle:Deposits')->findOneBy(array('uuid' => $uuid));
        $this->assertNotNull($deposits);
        $content = $deposits->getContent();
        $this->assertEquals(1, count($content));
    }
  
    //6.3.3. Creating a Resource with an Atom Entry
    public function testCreateMultipleResources()
    {
        $uuid = Uuid::v4();
        $depositXml = $this->createDepositXML($uuid);
        $this->addContentItem(
            $depositXml,
            'http://provider.example.com/3691/11186563486_8796f4f843_o_d.jpg'
        );

        $this->addContentItem(
            $depositXml,
            'http://provider.example.com/wm/paint/auth/monet/parliament/parliament.jpg'
        );

        $client = $this->postDeposit($depositXml, self::$provider->getUuid());
        $response = $client->getResponse();

        if($response->getStatusCode() !== 201) {            
            self::$logger->error($response->getContent());
        }
        
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertTrue($response->headers->has('Location'));
        $this->assertStringEndsWith($uuid . '/edit', $response->headers->get('location'));

        $deposits = self::$em->getRepository('LOCKSSOMaticCRUDBundle:Deposits')->findOneBy(array('uuid' => $uuid));
        $this->assertNotNull($deposits);
        $content = $deposits->getContent();
        $this->assertEquals(2, count($content));
        
        $deposit = self::$em->getRepository('LOCKSSOMaticCRUDBundle:Deposits')->findOneBy(
            array('uuid' => $uuid)
        );
        /** @var Content */
        $c1 = self::$em->getRepository('LOCKSSOMaticCRUDBundle:Content')->findOneBy(
            array(
                'url' => 'http://provider.example.com/3691/11186563486_8796f4f843_o_d.jpg',
                'deposit' => $deposit,
            )
        );
        /** @var Content */
        $c2 = self::$em->getRepository('LOCKSSOMaticCRUDBundle:Content')->findOneBy(
            array(
                'url' => 'http://provider.example.com/wm/paint/auth/monet/parliament/parliament.jpg',
                'deposit' => $deposit,
            )
        );
        
        $this->assertEquals($c1->getAu()->getId(), $c2->getAu()->getId());
    }
    
    public function testCreateMultipleAus() {
        $uuid = Uuid::v4();
        $depositXml = $this->createDepositXML($uuid);
        $this->addContentItem(
            $depositXml,
            'http://provider.example.com/3691/11186563486_8796f4f843_o_d.jpg',
            7000
        );

        $this->addContentItem(
            $depositXml,
            'http://provider.example.com/wm/paint/auth/monet/parliament/parliament.jpg',
            7000
            
        );

        $client = $this->postDeposit($depositXml, self::$provider->getUuid());
        $response = $client->getResponse();

        
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertTrue($response->headers->has('Location'));
        
        $em = static::$em;        
        $deposit = self::$em->getRepository('LOCKSSOMaticCRUDBundle:Deposits')->findOneBy(
            array('uuid' => $uuid)
        );

        /** @var Content */
        $c1 = self::$em->getRepository('LOCKSSOMaticCRUDBundle:Content')->findOneBy(
            array(
                'url' => 'http://provider.example.com/3691/11186563486_8796f4f843_o_d.jpg',
                'deposit' => $deposit,
            )
        );
        /** @var Content */
        $c2 = self::$em->getRepository('LOCKSSOMaticCRUDBundle:Content')->findOneBy(
            array(
                'url' => 'http://provider.example.com/wm/paint/auth/monet/parliament/parliament.jpg',
                'deposit' => $deposit,
            )
        );
        
        $this->assertNotEquals($c1->getAu()->getId(), $c2->getAu()->getId());
    }
    
    // fetch a deposit receipt
    public function testDepositReceiptAction()
    {
        $uuid = Uuid::v4();
        $depositXml = $this->createDepositXML($uuid);
        $this->addContentItem(
            $depositXml,
            'http://provider.example.com/3691/11186563486_8796f4f843_o_d.jpg'
        );

        $client = $this->postDeposit($depositXml, self::$provider->getUuid());
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        
        $crawler = $client->request('GET', '/api/sword/2.0/cont-iri/' . self::$provider->getUuid() . '/' . $uuid . '/edit');
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $recieptXml = $this->getSimpleXML($response->getContent());

        $this->assertEquals(1, count($recieptXml->xpath('atom:link[@rel="edit"]')));
        $this->assertGreaterThan(0, count($recieptXml->xpath('atom:link[@rel="edit-media"]')));
        $this->assertEquals(1, count($recieptXml->xpath('atom:link[@rel="http://purl.org/net/sword/terms/add"]')));
    }
    
    // fetch a deposit receipt with a bad contentProviderId.
    public function testDepositReceiptBadCollection()
    {
        $uuid = Uuid::v4();
        $client = static::createClient();

        $crawler = $client->request(
            'GET',
            '/api/sword/2.0/cont-iri/' . Uuid::v4() . '/' . $uuid . '/edit'
        );
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }
    
    // fetch a deposit receipt with a bad uuid.
    public function testDepositReceiptBadId()
    {
        $uuid = Uuid::v4();
        $client = static::createClient();
        
        $crawler = $client->request(
            'GET',
            '/api/sword/2.0/cont-iri/' . self::$provider->getUuid() . '/' . $uuid . '/edit'
        );
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
    
    public function testViewDepositAction()
    {
        $uuid = Uuid::v4();

        $depositXml = $this->createDepositXML($uuid);
        $this->addContentItem(
            $depositXml,
            'http://provider.example.com/3691/11186563486_8796f4f843_o_d.jpg'
        );

        $client = $this->postDeposit($depositXml, self::$provider->getUuid());
        $client->request(
            'GET',
            '/api/sword/2.0/cont-iri/' . self::$provider->getUuid() . '/' . $uuid
        );

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseXml = $this->getSimpleXML($response->getContent());
        $this->assertEquals(1, count($responseXml->xpath('//lom:content')));
    }
    
    // fetch a deposit receipt with a bad contentProviderId.
    public function testViewDepositBadCollection()
    {
        $uuid = Uuid::v4();
        $client = static::createClient();
        $client->request('GET', '/api/sword/2.0/cont-iri/' . Uuid::v4() . '/' . $uuid);
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }
    
    // fetch a deposit receipt with a bad uuid.
    public function testViewDepositBadId()
    {
        $uuid = Uuid::v4();
        $client = static::createClient();
        
        $crawler = $client->request(
            'GET',
            '/api/sword/2.0/cont-iri/' . self::$provider->getUuid() . '/' . $uuid
        );
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
    
    public function testEditDepositAction()
    {
        $url = 'http://provider.example.com/3691/11186563486_8796f4f843_o_d.jpg';
        $uuid = Uuid::v4();
        $depositXml = $this->createDepositXML($uuid);
        $this->addContentItem(
            $depositXml,
            $url
        );

        $client = $this->postDeposit($depositXml, self::$provider->getUuid());
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        
        $depositXml->children(Namespaces::LOM)->content[0]->addAttribute('recrawl', 'false');
        
        $crawler = $client->request(
            'PUT',
            '/api/sword/2.0/cont-iri/' . self::$provider->getUuid() . '/' . $uuid . '/edit',
            array(),
            array(),
            array(),
            $depositXml->asXML()
        );
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        
        $deposit = self::$em->getRepository('LOCKSSOMaticCRUDBundle:Deposits')->findOneBy(
            array('uuid' => $uuid)
        );
        $content = self::$em->getRepository('LOCKSSOMaticCRUDBundle:Content')->findOneBy(
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
        $url = 'http://provider.example.com/3691/11186563486_8796f4f843_o_d.jpg';
        $uuid = Uuid::v4();
        $depositXml = $this->createDepositXML($uuid);
        $this->addContentItem(
            $depositXml,
            $url
        );
        
        $client = static::createClient();
        $crawler = $client->request(
            'PUT',
            '/api/sword/2.0/cont-iri/'. Uuid::v4() . '/' . $uuid . '/edit',
            array(),
            array(),
            array(),
            $depositXml->asXML()
        );
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }
}
