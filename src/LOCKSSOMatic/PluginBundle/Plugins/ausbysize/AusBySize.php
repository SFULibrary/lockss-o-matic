<?php

namespace LOCKSSOMatic\PluginBundle\Plugins\ausbysize;

use LOCKSSOMatic\PluginBundle\Event\ServiceDocumentEvent;
use SimpleXMLElement;

class AusBySize {
    
    public function onServiceDocument(ServiceDocumentEvent $event) {
        $xml = $event->getXml();
        $xml->addChild('child', 'ausbysize');
    }
    
}