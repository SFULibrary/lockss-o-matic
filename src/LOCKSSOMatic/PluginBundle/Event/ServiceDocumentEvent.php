<?php

namespace LOCKSSOMatic\PluginBundle\Event;

use SimpleXMLElement;
use Symfony\Component\EventDispatcher\Event;

class ServiceDocumentEvent extends Event {
    
    protected $xml;
    
    public function __construct(SimpleXMLElement $xml) {
        $this->xml = $xml;
    }
    
    public function getXml() {
        return $this->xml;
    }
    
}
