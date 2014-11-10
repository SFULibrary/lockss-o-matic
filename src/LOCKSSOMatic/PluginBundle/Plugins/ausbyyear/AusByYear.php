<?php

namespace LOCKSSOMatic\PluginBundle\Plugins\ausbyyear;

use LOCKSSOMatic\CRUDBundle\Entity\Aus;
use LOCKSSOMatic\CRUDBundle\Entity\ContentBuilder;
use LOCKSSOMatic\CRUDBundle\Entity\ContentProviders;
use LOCKSSOMatic\PluginBundle\Event\DepositContentEvent;
use LOCKSSOMatic\PluginBundle\Event\ServiceDocumentEvent;
use LOCKSSOMatic\PluginBundle\Plugins\AbstractPlugin;
use LOCKSSOMatic\SWORDBundle\Utilities\Namespaces;
use SimpleXMLElement;

/**
 * Organize AUs by year published.
 */
class AusByYear extends AbstractPlugin
{
    /**
     * @todo Convert this to ContainerAware:
     * 
     * http://stackoverflow.com/questions/17126277/how-to-give-container-as-argument-to-services
     */

    /**
     * Automatically called when a service document is requested.
     * 
     * @param ServiceDocumentEvent $event
     */
    public function onServiceDocument(ServiceDocumentEvent $event)
    {
        /** @var SimpleXMLElement */
        $xml = $event->getXml();
        $plugin = $xml->addChild('plugin', null, Namespaces::LOM);
        $plugin->addAttribute('attributes', 'year');
        $plugin->addAttribute('pluginId', $this->getPluginId());
    }

    /**
     * {@inheritdoc}
     */
    public function onDepositContent(DepositContentEvent $event)
    {
        /** @var ContentProviders */
        $contentProvider = $event->getContentProvider();
        $deposit = $event->getDeposit();
        $contentXml = $event->getXml();

        $maxSize = $contentProvider->getMaxAuSize();
        $contentSize = (string) $contentXml->attributes()->size;
        $contentYear = (string) $contentXml->attributes()->year;

        $self = $this;
        $filter = function(Aus $au) use ($self, $maxSize, $contentSize, $contentYear) {
            if ($au->getContentSize() + $contentSize >= $maxSize) {
                return false;
            }
            $data = $self->getData('AuParams', $au);
            if ($data === false) {
                return false;
            }
            if ($data['ByYear'] === true && $data['year'] === $contentYear) {
                return true;
            }
            return false;
        };

        $this->container->get('doctrine')->getManager()->refresh($contentProvider);
        $aus = $contentProvider->getAus()->filter($filter);
        if ($aus->count() >= 1) {
            $au = $aus->first();
        } else {
            $au = new Aus();
            $au->setContentProvider($contentProvider);
            $au->setManaged(true);
            $au->setAuid('auid-year-abr-' . $contentYear);
            $au->setManifestUrl('http://pln.example.com/foo/year');
            $this->container->get('doctrine')->getManager()->persist($au);
            $this->container->get('doctrine')->getManager()->flush();
            $this->setData('AuParams', $au, array('ByYear' => true, 'year' => $contentYear));
        }
        $contentBuilder = new ContentBuilder();
        $content = $contentBuilder->fromSimpleXML($contentXml);
        $content->setDeposit($deposit);
        $content->setAu($au);
        $this->container->get('doctrine')->getManager()->persist($content);
        $au->addContent($content);
        $this->container->get('doctrine')->getManager()->flush();
        return $au;
    }

    public function getDescription()
    {
        return "Organize archival units by year.";
    }

    public function getName()
    {
        return "AUsByYear";
    }

}
