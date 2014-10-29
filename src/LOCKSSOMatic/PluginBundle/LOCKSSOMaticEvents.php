<?php

namespace LOCKSSOMatic\PluginBundle;

// http://symfony.com/doc/current/components/event_dispatcher/introduction.html

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