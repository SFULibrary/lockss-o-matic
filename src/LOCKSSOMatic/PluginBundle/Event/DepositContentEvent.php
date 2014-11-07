<?php

namespace LOCKSSOMatic\PluginBundle\Event;

use LOCKSSOMatic\CRUDBundle\Entity\ContentProviders;
use LOCKSSOMatic\CRUDBundle\Entity\Deposits;
use SimpleXMLElement;
use Symfony\Component\EventDispatcher\Event;

class DepositContentEvent extends Event {
    
    protected $xml;
    
    protected $provider;

    protected $deposit;
    
    public function __construct(Deposits $deposit, ContentProviders $provider, SimpleXMLElement $xml) {
        $this->deposit = $deposit;
        $this->provider = $provider;
        $this->xml = $xml;
    }

    public function getDeposit() {
        return $this->deposit;
    }
    
    public function getContentProvider() {
        return $this->provider;
    }
    
    public function getXml() {
        return $this->xml;
    }
    
}
