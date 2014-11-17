<?php

/* 
 * The MIT License
 *
 * Copyright 2014. Michael Joyce <ubermichael@gmail.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace LOCKSSOMatic\SWORDBundle\Event;

use LOCKSSOMatic\CRUDBundle\Entity\ContentProviders;
use LOCKSSOMatic\CRUDBundle\Entity\Deposits;
use SimpleXMLElement;
use Symfony\Component\EventDispatcher\Event;

/**
 * This event is fired when a new deposit is received in the SWORDBundle default
 * controller.
 */
class DepositContentEvent extends Event {
    
    /**
     * @var string
     */
    protected $pluginName;
    
    /**
     * @var SimpleXMLElement
     */
    protected $xml;

    /**
     * @var ContentProviders
     */
    protected $provider;
    
    /**
     * @var Deposits
     */
    protected $deposit;

    /**
     * Construct the event
     * 
     * @param Deposits $deposit
     * @param ContentProviders $provider
     * @param SimpleXMLElement $xml
     */
    public function __construct($pluginName, Deposits $deposit, ContentProviders $provider, SimpleXMLElement $xml) {
        $this->pluginName = $pluginName;
        $this->deposit = $deposit;
        $this->provider = $provider;
        $this->xml = $xml;
    }
    
    public function getPluginName() {
        return $this->pluginName;
    }

    /**
     * Get the deposit for the event
     * 
     * @return Deposits
     */
    public function getDeposit() {
        return $this->deposit;
    }

    /**
     * Get the content provider for the event.
     * 
     * @return ContentProviders
     */
    public function getContentProvider() {
        return $this->provider;
    }
    
    /**
     * Get the lom:content XML fragment for this event.
     * 
     * @return SimpleXMLElement
     */
    public function getXml() {
        return $this->xml;
    }
    
}
