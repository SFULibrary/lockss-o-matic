<?php

namespace LOCKSSOMatic\SwordBundle\Plugins;

use LOCKSSOMatic\CrudBundle\Entity\Au;
use LOCKSSOMatic\CrudBundle\Entity\ContentProvider;
use LOCKSSOMatic\CrudBundle\Entity\Deposit;
use LOCKSSOMatic\CrudBundle\Utility\ContentBuilder;
use LOCKSSOMatic\PluginBundle\Plugins\AbstractPlugin;
use LOCKSSOMatic\SwordBundle\Event\DepositContentEvent;
use LOCKSSOMatic\SwordBundle\Event\ServiceDocumentEvent;
use LOCKSSOMatic\SwordBundle\Utilities\Namespaces;
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

    public function buildAu(ContentProvider $provider, $comment, $manifest)
    {
        $em = $this->container->get('doctrine')->getManager();
        $au = new Au();
        $au->setContentProvider($provider);
        $au->setManaged(true);
        $au->setPln($provider->getPln());
        $au->setPlugin($provider->getContentOwner()->getPlugin());
        $au->setComment($comment);
        $au->setManifestUrl($manifest);
        $em->persist($au);
        $em->flush($au);
        return $au;
    }

    public function depositContent(SimpleXMLElement $contentXml, Deposit $deposit, Au $au) {
        $em = $this->container->get('doctrine')->getManager();
        $contentBuilder = new ContentBuilder();
        $content = $contentBuilder->fromSimpleXML($contentXml);
        $content->setDeposit($deposit);
        $content->setAu($au);
        $au->addContent($content);
        $em->persist($content);
        $em->flush($content);
    }

}
