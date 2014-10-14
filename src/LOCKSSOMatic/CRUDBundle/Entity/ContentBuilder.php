<?php

namespace LOCKSSOMatic\CRUDBundle\Entity;

use LOCKSSOMatic\CRUDBundle\Entity\Content;
use LOCKSSOMatic\SWORDBundle\Utilities\Namespaces;
use \SimpleXMLElement;

class ContentBuilder {
    
    /**
     * 
     * @param SimpleXMLElement $xml
     * @return Content
     */
    public function fromSimpleXML(SimpleXMLElement $xml) {
        $content = new Content();
        $content->setSize($xml->attributes()->size);
        $content->setChecksumType($xml->attributes()->checksumType);
        $content->setChecksumValue($xml->attributes()->checksumValue);
        $content->setUrl((string)$xml);
        $content->setTitle('Some generated title');
        
        return $content;
    }
    
}