<?php

namespace LOCKSSOMatic\CRUDBundle\Entity;

use LOCKSSOMatic\CRUDBundle\Entity\Deposits;
use LOCKSSOMatic\SWORDBundle\Utilities\Namespaces;
use SimpleXMLElement;

class DepositBuilder
{

    /**
     * 
     * @param SimpleXMLElement $xml
     * @return Deposits
     */
    public function fromSimpleXML(SimpleXMLElement $xml)
    {
        $deposit = new Deposits();
        $ns = new Namespaces();
        $id = preg_replace('/^urn:uuid:/', '', $xml->children($ns::ATOM)->id[0]);
        $title = $xml->children($ns::ATOM)->title[0];

        $deposit->setTitle((string) $title);
        $deposit->setUuid($id);

        return $deposit;
    }

}
