<?php

namespace LOCKSSOMatic\SwordBundle\Documents;

use SimpleXMLElement;

class ServiceDocument {

    private $version;
    private $maxUPloadSize;
    private $accept;
    private $multipartAccept;
    private $collectionPolicy;
    private $mediation;
    private $treatment;
    private $acceptPackaging;
    private $services;

    public function __construct(SimpleXMLElement $xml = null) {
        if($xml === null) {
            return;
        }
        // get the fields from the xml.
    }

    /**
     * @return string
     */
    public function serialize() {
        
    }

    function getVersion() {
        return $this->version;
    }

    function getMaxUPloadSize() {
        return $this->maxUPloadSize;
    }

    function getAccept() {
        return $this->accept;
    }

    function getMultipartAccept() {
        return $this->multipartAccept;
    }

    function getCollectionPolicy() {
        return $this->collectionPolicy;
    }

    function getMediation() {
        return $this->mediation;
    }

    function getTreatment() {
        return $this->treatment;
    }

    function getAcceptPackaging() {
        return $this->acceptPackaging;
    }

    function getServices() {
        return $this->services;
    }

    function setVersion($version) {
        $this->version = $version;
    }

    function setMaxUPloadSize($maxUPloadSize) {
        $this->maxUPloadSize = $maxUPloadSize;
    }

    function setAccept($accept) {
        $this->accept = $accept;
    }

    function setMultipartAccept($multipartAccept) {
        $this->multipartAccept = $multipartAccept;
    }

    function setCollectionPolicy($collectionPolicy) {
        $this->collectionPolicy = $collectionPolicy;
    }

    function setMediation($mediation) {
        $this->mediation = $mediation;
    }

    function setTreatment($treatment) {
        $this->treatment = $treatment;
    }

    function setAcceptPackaging($acceptPackaging) {
        $this->acceptPackaging = $acceptPackaging;
    }

    function setServices($services) {
        $this->services = $services;
    }


}