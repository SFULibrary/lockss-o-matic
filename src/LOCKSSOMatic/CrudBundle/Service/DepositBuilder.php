<?php


/*
 * The MIT License
 *
 * Copyright 2014-2016. Michael Joyce <ubermichael@gmail.com>.
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

namespace LOCKSSOMatic\CrudBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectManager;
use J20\Uuid\Uuid;
use LOCKSSOMatic\CrudBundle\Entity\ContentProvider;
use LOCKSSOMatic\CrudBundle\Entity\Deposit;
use LOCKSSOMatic\SwordBundle\Utilities\Namespaces;
use Monolog\Logger;
use SimpleXMLElement;
use Symfony\Component\Form\Form;

/**
 * Constructs a single deposit object from either form data or a SWORD deposit's
 * XML payload.
 * 
 * This class doesn't build the corresponding content objects, that must be done
 * with a call to ContentBuilder.
 */
class DepositBuilder
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @var AuBuilder
     */
    private $auBuilder;

    /**
     * Set the logger.
     * 
     * @param Logger $logger
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Set the entity manager by way of a poorly named function.
     * 
     * @param Registry $registry
     */
    public function setRegistry(Registry $registry)
    {
        $this->em = $registry->getManager();
    }

    /**
     * Set the AU Builder.
     * 
     * @param AuBuilder $auBuilder
     */
    public function setAuBuilder(AuBuilder $auBuilder)
    {
        $this->auBuilder = $auBuilder;
    }

    /**
     * Build a deposit from an XML element.
     * 
     * @param SimpleXMLElement $xml
     *
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
        $deposit->setDepositDate();

        if ($this->em !== null) {
            $this->em->persist($deposit);
        }

        return $deposit;
    }

    /**
     * Build a deposit from a submitted form.
     * 
     * @param Form $form
     * @param ContentProvider $provider
     * 
     * @return Deposit
     */
    public function fromForm(Form $form, ContentProvider $provider)
    {
        $data = $form->getData();

        $deposit = new Deposit();
        $deposit->setTitle($data['title']);
        $deposit->setSummary($data['summary']);
        $deposit->setContentProvider($provider);
        $deposit->setDepositDate();

        if (array_key_exists('uuid', $data) && $data['uuid'] !== null && $data['uuid'] !== '') {
            $deposit->setUuid($data['uuid']);
        } else {
            $deposit->setUuid(Uuid::v4());
        }

        /* @var SplFileInfo $dataFile */

        if ($this->em !== null) {
            $this->em->persist($deposit);
            $this->em->flush();
        }

        return $deposit;
    }
}
