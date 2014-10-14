<?php

namespace LOCKSSOMatic\SWORDBundle\Tests\Utilities;

use LOCKSSOMatic\SWORDBundle\Utilities\Namespaces;
use PHPUnit_Framework_TestCase;
use SimpleXMLElement;

class NamespacesTest extends PHPUnit_Framework_TestCase
{

    public function testGetNamespace()
    {
        $ns = new Namespaces();
        $this->assertEquals('http://purl.org/dc/terms/', $ns->getNamespace('DCTERMS'));
        $this->assertEquals('http://purl.org/dc/terms/', $ns->getNamespace('dcterms'));
        $this->assertNull($ns->getNamespace('FOO'));
    }

    public function testRegisterNamespaces() {
        
        $ns = new Namespaces();
        $xml = new SimpleXMLElement('<foo />');
        $ns->registerNamespaces($xml);
        
        // the attribute needs a prefix, but it doesn't have to match the 
        // default namespace prefix.
        $xml->addAttribute('dd:a', "foooo", $ns::DCTERMS);
        
        $attr = array_shift($xml->xpath('/foo/@dcterms:a'));
        $this->assertEquals('foooo', $attr);
    }
    
}
