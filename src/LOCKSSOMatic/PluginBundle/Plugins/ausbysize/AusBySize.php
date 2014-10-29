<?php

namespace LOCKSSOMatic\PluginBundle\Plugins;

use SimpleXMLElement;

class AusBySize {
    
    public function onServiceDocument(SimpleXMLElement $xml) {
        return $xml;
    }
    
}