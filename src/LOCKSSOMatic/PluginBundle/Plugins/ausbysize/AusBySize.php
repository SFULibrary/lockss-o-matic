<?php

namespace LOCKSSOMatic\PluginBundle\Plugins\ausbysize;

use Doctrine\Common\Util\Debug;
use LOCKSSOMatic\CRUDBundle\Entity\Aus;
use LOCKSSOMatic\CRUDBundle\Entity\ContentBuilder;
use LOCKSSOMatic\CRUDBundle\Entity\ContentProviders;
use LOCKSSOMatic\PluginBundle\Event\DepositContentEvent;
use LOCKSSOMatic\PluginBundle\Event\ServiceDocumentEvent;
use LOCKSSOMatic\PluginBundle\Plugins\AbstractPlugin;
use LOCKSSOMatic\SWORDBundle\Utilities\Namespaces;
use SimpleXMLElement;

/**
 * Organize AUs by size.
 */
class AusBySize extends AbstractPlugin
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
        $plugin->addAttribute('attributes', 'size');
        $plugin->addAttribute('pluginId', $this->getPluginId());
    }

    // puts the content item into an au.
    public function onDepositContent(DepositContentEvent $event)
    {
        /** @var ContentProviders */
        $contentProvider = $event->getContentProvider();
        $deposit = $event->getDeposit();
        $contentXml = $event->getXml();

        $maxSize = $contentProvider->getMaxAuSize();
        $contentSize = (string)$contentXml->attributes()->size;

        // hack around a PHP 5.3 bug.
        $self = $this;

        $filter = function(Aus $au) use($self, $maxSize, $contentSize) {
            if ($au->getContentSize() + $contentSize >= $maxSize) {
                return false;
            }
            $data = $self->getData('AuParams', $au);
            if ($data === null) {
                return false;
            }
            return true;
        };

        $this->container->get('doctrine')->getManager()->refresh($contentProvider);
        $aus = $contentProvider->getAus()->filter($filter);
        if ($aus->count() >= 1) {
            $au = $aus->first();
        } else {
            $au = new Aus();
            $au->setContentProvider($contentProvider);
            $au->setManaged(true);
            $au->setAuid('some generated auid - size - odc');
            $au->setManifestUrl('http://pln.example.com/foo/bar');
            $this->container->get('doctrine')->getManager()->persist($au);
            $this->container->get('doctrine')->getManager()->flush();
            $this->setData('AuParams', $au, array('ByYear' => true));
        }
        $contentBuilder = new ContentBuilder();
        $content = $contentBuilder->fromSimpleXML($contentXml);
        $content->setDeposit($deposit);
        $content->setAu($au);
        $this->container->get('doctrine')->getManager()->persist($content);
        $au->addContent($content);
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
