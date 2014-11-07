<?php

namespace LOCKSSOMatic\PluginBundle\Plugins\ausbysize;

use Doctrine\Common\Util\Debug;
use LOCKSSOMatic\CRUDBundle\Entity\Aus;
use LOCKSSOMatic\CRUDBundle\Entity\ContentBuilder;
use LOCKSSOMatic\CRUDBundle\Entity\ContentProviders;
use LOCKSSOMatic\PluginBundle\Event\DepositContentEvent;
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

    // puts the content item into an au.
    public function onDepositContent(DepositContentEvent $event)
    {
        /** @var ContentProviders */
        $deposit = $event->getDeposit();
        $contentProvider = $event->getContentProvider();
        $contentXml = $event->getXml();

        $maxSize = $contentProvider->getMaxAuSize();
        $contentSize = $contentXml->attributes()->size;

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
            $this->container->get('doctrine')->getManager()->persist($au);
            $au->setContentProvider($contentProvider);
            $au->setManaged(true);
            $au->setAuid('some generated auid - size - odc');
            $au->setManifestUrl('http://pln.example.com/foo/bar');
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
        $maxSize = $contentProvider->getMaxAuSize();
        $contentSize = $contentXml->attributes()->size;

        // hack around a PHP 5.3 bug.
        $self = $this;

        $filter = function(AUs $au) use($self, $maxSize, $contentSize) {
            if ($au->getContentSize() + $contentSize >= $maxSize) {
                return false;
            }
            $data = $self->getData('AuParams', $au);
            if ($data === null) {
                return false;
            }
            return true;
        };

        $aus = $contentProvider->getAus()->filter($filter);
        if ($aus->count() >= 1) {
            return $aus->first();
        }

        $au = new Aus();
        $this->container->get('doctrine')->getManager()->persist($au);
        $au->setContentProvider($contentProvider);
        $au->setManaged(true);
        $au->setAuid('some generated auid - size - gda');
        $au->setManifestUrl('http://pln.example.com/foo/bar');
        $this->container->get('doctrine')->getManager()->flush();

        $this->setData('AuParams', $au, array('ByYear' => true));
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
