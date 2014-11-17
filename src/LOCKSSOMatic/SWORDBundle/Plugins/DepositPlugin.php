<?php

namespace LOCKSSOMatic\SWORDBundle\Plugins;

use LOCKSSOMatic\CRUDBundle\Entity\Aus;
use LOCKSSOMatic\CRUDBundle\Entity\ContentBuilder;
use LOCKSSOMatic\CRUDBundle\Entity\ContentProviders;
use LOCKSSOMatic\CRUDBundle\Entity\Deposits;
use LOCKSSOMatic\PluginBundle\Plugins\AbstractPlugin;
use LOCKSSOMatic\SWORDBundle\Event\DepositContentEvent;
use LOCKSSOMatic\SWORDBundle\Event\ServiceDocumentEvent;
use LOCKSSOMatic\SWORDBundle\Utilities\Namespaces;
use SimpleXMLElement;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DepositPlugin
 *
 * @author Michael Joyce <michael@negativespace.net>
 */
abstract class DepositPlugin extends AbstractPlugin
{

    public function requiredAttributes()
    {
        return array('size');
    }

    public function onServiceDocument(ServiceDocumentEvent $event)
    {
        $xml = $event->getXml();
        $plugin = $xml->addChild('plugin', null, Namespaces::LOM);
        $plugin->addAttribute('pluginId', $this->getPluginId());
        $attributes = $this->requiredAttributes();
        asort($attributes);
        $plugin->addAttribute('attributes', implode(', ', $attributes));
    }

    public function onDepositContent(DepositContentEvent $event)
    {
        if ($event->getPluginName() !== $this->getPluginId()) {
            return false;
        }
        return true;
    }

    public function buildAu(ContentProviders $provider, $auid, $comment, $manifest)
    {
        $au = new Aus();
        $au->setContentProvider($provider);
        $au->setManaged(true);
        $au->setAuid($auid);
        $au->setComment($comment);
        $au->setManifestUrl($manifest);
        $this->container->get('doctrine')->getManager()->persist($au);
        $this->container->get('doctrine')->getManager()->flush();
        return $au;
    }

    public function depositContent(SimpleXMLElement $contentXml, Deposits $deposit, Aus $au) {
        $contentBuilder = new ContentBuilder();
        $content = $contentBuilder->fromSimpleXML($contentXml);
        $content->setDeposit($deposit);
        $content->setAu($au);
        $this->container->get('doctrine')->getManager()->persist($content);
        $au->addContent($content);
        $this->container->get('doctrine')->getManager()->flush();
    }

}
