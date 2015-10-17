<?php

namespace LOCKSSOMatic\CrudBundle\Utility;

use Doctrine\Common\Persistence\ObjectManager;
use J20\Uuid\Uuid;
use LOCKSSOMatic\CrudBundle\Entity\ContentProvider;
use LOCKSSOMatic\CrudBundle\Entity\Deposit;
use LOCKSSOMatic\SwordBundle\Utilities\Namespaces;
use SimpleXMLElement;
use Symfony\Component\Form\Form;

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

    public function fromForm(Form $form, ContentProvider $provider, ObjectManager $em = null) {
        $data = $form->getData();
        $deposit = new Deposit();
        $deposit->setTitle($data['title']);
        $deposit->setSummary($data['summary']);
        $deposit->setContentProvider($provider);

        if($data['uuid'] !== null && $data['uuid'] !== '') {
            $deposit->setUuid($data['uuid']);
        } else {
            $deposit->setUuid(Uuid::v4());
        }

        $dataFile = $data['file'];
        $fh = $dataFile->openFile();
        $headers = $fh->fgetcsv();
        // map required and other parameters to indexes in $headers.
        // use those indexes to build out the content.

        if($em !== null) {
            $em->persist($deposit);
            $em->flush($deposit);
        }
        return $deposit;
    }

}
