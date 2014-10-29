<?php

namespace LOCKSSOMatic\PluginBundle\Plugins\ausbyyear;

use LOCKSSOMatic\PluginBundle\Event\ServiceDocumentEvent;
use LOCKSSOMatic\SWORDBundle\Utilities\Namespaces;
use ReflectionClass;
use SimpleXMLElement;

class AusByYear {

    private $name;
    
    public function __construct() {
        $reflection = new ReflectionClass($this);        
        $this->name = $reflection->getShortName();
    }
    
    public function onServiceDocument(ServiceDocumentEvent $event) {
        /** @var SimpleXMLElement */
        $xml = $event->getXml();
        $plugin = $xml->addChild('plugin', null, Namespaces::LOM);
        $plugin->addAttribute('name', $this->name);
        $plugin->addAttribute('attributes', 'year');
    }
    
}