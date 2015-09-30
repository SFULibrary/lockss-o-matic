<?php

namespace LOCKSSOMatic\CrudBundle\Utility;

use Doctrine\Common\Persistence\ObjectManager;
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
    public function fromSimpleXML(SimpleXMLElement $xml, ObjectManager $em = null)
    {
        $deposit = new Deposit();
        $ns = new Namespaces();
        $id = preg_replace('/^urn:uuid:/', '', $xml->children($ns::ATOM)->id[0]);
        $title = $xml->children($ns::ATOM)->title[0];

        $deposit->setTitle((string) $title);
        $deposit->setUuid($id);

        if($em !== null) {
            $em->persist($deposit);
        }

        return $deposit;
    }

}
