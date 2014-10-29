<?php

namespace LOCKSSOMatic\PluginBundle\Plugins;

use LOCKSSOMatic\CRUDBundle\Entity\Aus;
use LOCKSSOMatic\CRUDBundle\Entity\ContentProviders;
use SimpleXMLElement;

/**
 * If a plugin can find an AU for deposit, it should implement this
 * interface.
 */
interface DestinationAuInterface {
    
    /**
     * Get the destination AU for a deposit item.
     * 
     * @param ContentProviders $contentProvider
     * @param SimpleXMLElement $contentXml
     * 
     * @return Aus
     */
    public function getDestinationAu(ContentProviders $contentProvider, SimpleXMLElement $contentXml);
    
}