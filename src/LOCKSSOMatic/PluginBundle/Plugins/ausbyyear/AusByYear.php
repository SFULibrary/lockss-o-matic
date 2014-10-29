<?php

namespace LOCKSSOMatic\PluginBundle\Plugins\ausbyyear;

use LOCKSSOMatic\CRUDBundle\Entity\ContentProviders;
use LOCKSSOMatic\PluginBundle\Event\ServiceDocumentEvent;
use LOCKSSOMatic\PluginBundle\Plugins\AbstractPlugin;
use LOCKSSOMatic\PluginBundle\Plugins\DestinationAuInterface;
use LOCKSSOMatic\SWORDBundle\Utilities\Namespaces;
use SimpleXMLElement;

/**
 * Organize AUs by year published.
 */
class AusByYear extends AbstractPlugin implements DestinationAuInterface {

    /**
     * Automatically called when a service document is requested.
     * 
     * @param ServiceDocumentEvent $event
     */
    public function onServiceDocument(ServiceDocumentEvent $event) {
        /** @var SimpleXMLElement */
        $xml = $event->getXml();
        $plugin = $xml->addChild('plugin', null, Namespaces::LOM);
        $plugin->addAttribute('name', $this->name);
        $plugin->addAttribute('attributes', 'year');
    }

    /**
     * {@inheritdoc}
     */
    public function getDestinationAu(ContentProviders $contentProvider, SimpleXMLElement $contentXml)
    {
        // find the content provider's open au for the content item.
        // $contentXml must include a year attribute.
    }

}