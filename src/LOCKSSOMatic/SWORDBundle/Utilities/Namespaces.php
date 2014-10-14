<?php

namespace LOCKSSOMatic\SWORDBundle\Utilities;

use ReflectionClass;
use SimpleXMLElement;

class Namespaces {
    
    const DCTERMS = 'http://purl.org/dc/terms/';    
    const SWORD = 'http://purl.org/net/sword/terms/';    
    const ATOM = 'http://www.w3.org/2005/Atom';    
    const LOM = 'http://lockssomatic.info/SWORD2';    
    const RDF = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#';    
    const APP = 'http://www.w3.org/2007/app';
    
    public function getNamespace($prefix) {
        $constant = get_class() . '::'. strtoupper($prefix);
        if( ! defined($constant)) {
            return null;
        } 
        return constant($constant);
    }
    
    public function registerNamespaces(SimpleXMLElement $xml) {
        $refClass = new ReflectionClass(__CLASS__);
        $constants = $refClass->getConstants();
        foreach(array_keys($constants) as $key) {
            $prefix = strtolower($key);
            $xml->registerXPathNamespace($prefix, $this->getNamespace($prefix));
        }
    }    
    
}