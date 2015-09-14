<?php

namespace LOCKSSOMatic\CrudBundle\Utility;

use LOCKSSOMatic\CrudBundle\Entity\Deposit;
use LOCKSSOMatic\SwordBundle\Utilities\Namespaces;
use SimpleXMLElement;

class DepositBuilder
{

    /**
     *
     * @param SimpleXMLElement $xml
     * @return Deposit
     */
    public function fromSimpleXML(SimpleXMLElement $xml)
    {
        $deposit = new Deposit();
        $ns = new Namespaces();
        $id = preg_replace('/^urn:uuid:/', '', $xml->children($ns::ATOM)->id[0]);
        $title = $xml->children($ns::ATOM)->title[0];

        $deposit->setTitle((string) $title);
        $deposit->setUuid($id);

        return $deposit;
    }

}