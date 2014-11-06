<?php

namespace LOCKSSOMatic\PluginBundle\Event;

use LOCKSSOMatic\CRUDBundle\Entity\ContentProviders;
use SimpleXMLElement;
use Symfony\Component\EventDispatcher\Event;

class DepositContentEvent extends Event {
    
    protected $xml;
    
    protected $provider;
    
    public function __construct(ContentProviders $provider, SimpleXMLElement $xml) {
        $this->provider = $provider;
        $this->xml = $xml;
    }
    
    public function getContentProvider() {
        return $this->provider;
    }
    
    public function getXml() {
        return $this->xml;
    }
    
}
