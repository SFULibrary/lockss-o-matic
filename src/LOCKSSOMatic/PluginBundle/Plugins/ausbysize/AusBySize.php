<?php

namespace LOCKSSOMatic\PluginBundle\Plugins\ausbysize;

use LOCKSSOMatic\CRUDBundle\Entity\Aus;
use LOCKSSOMatic\CRUDBundle\Entity\ContentProviders;
use LOCKSSOMatic\PluginBundle\Event\ServiceDocumentEvent;
use LOCKSSOMatic\PluginBundle\Plugins\AbstractPlugin;
use LOCKSSOMatic\PluginBundle\Plugins\DestinationAuInterface;
use LOCKSSOMatic\SWORDBundle\Utilities\Namespaces;
use SimpleXMLElement;

/**
 * Organize AUs by size.
 */
class AusBySize extends AbstractPlugin implements DestinationAuInterface
{

    /**
     * This method is automatically called when a service document
     * is requested.
     * 
     * @param ServiceDocumentEvent $event
     */
    public function onServiceDocument(ServiceDocumentEvent $event)
    {
        /** @var SimpleXMLElement */
        $xml = $event->getXml();
        $plugin = $xml->addChild('plugin', null, Namespaces::LOM);
        $plugin->addAttribute('name', get_class($this));
        $plugin->addAttribute('attributes', 'size');
    }

    /**
     * Determines which AU to put the content in, based on the size of the 
     * content item and the content provider's maxAuSize.
     *
     * @param ContentProviders $contentProvider The content provider for the deposit
     * @param SimpleXMLElement $contentXml the XML fragment describing the content item.
     * 
     * @return Aus $au.
     */
    public function getDestinationAu(ContentProviders $contentProvider, SimpleXMLElement $contentXml)
    {
        $aus = $contentProvider->getAus();
        if ($aus->count() >= 1) {
            $au = $aus->last();
            if ($au->getContentSize() + $contentXml->attributes()->size < $contentProvider->getMaxAuSize()) {
                return $au;
            }
        }

        $au = new Aus();
        $this->container->get('doctrine')->getManager()->persist($au);
        $au->setContentProvider($contentProvider);
        $au->setManaged(true);
        $au->setAuid('some generated auid.');
        $au->setManifestUrl('http://pln.example.com/foo/bar');
        $this->container->get('doctrine')->getManager()->flush();

        $this->setData($au, 'AuParams', array('providerId' => $contentProvider->getUuid()));
        return $au;
    }

    public function getDescription()
    {
        return "Organize archival units by size.";
    }

    public function getName()
    {
        return "AUsBySize";
    }

}
