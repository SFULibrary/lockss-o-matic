<?php

namespace LOCKSSOMatic\PluginBundle;

/**
 * Define the event names as constants and document them.
 */
final class LOCKSSOMaticEvents {
    
    /**
     * The sword.servicedoc event is thrown each time a valid 
     * service document request is processed.
     * 
     * This event listener will be passed
     * 
     * LOCKSSOMatic\PluginBundle\Event\ServiceDocumentEvent 
     * SimpleXmlElement
     */
    const SERVICE_DOCUMENT = 'sword.servicedoc';
    
}