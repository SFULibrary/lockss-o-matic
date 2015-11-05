<?php

namespace LOCKSSOMatic\CrudBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectManager;
use J20\Uuid\Uuid;
use LOCKSSOMatic\CrudBundle\Entity\Content;
use LOCKSSOMatic\CrudBundle\Entity\ContentProperty;
use LOCKSSOMatic\CrudBundle\Entity\ContentProvider;
use LOCKSSOMatic\CrudBundle\Entity\Deposit;
use LOCKSSOMatic\SwordBundle\Utilities\Namespaces;
use Monolog\Logger;
use SimpleXMLElement;
use SplFileInfo;
use Symfony\Component\Form\Form;

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
    
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }
    
    public function setRegistry(Registry $registry)
    {
        $this->em = $registry->getManager();
    }

    public function setAuBuilder(AuBuilder $auBuilder)
    {
        $this->auBuilder = $auBuilder;
    }
    
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

        if ($this->em !== null) {
            $this->em->persist($deposit);
        }

        return $deposit;
    }

    public function fromForm(Form $form, ContentProvider $provider)
    {
        $data = $form->getData();
        $plugin = $provider->getPlugin();
        
        $deposit = new Deposit();
        $deposit->setTitle($data['title']);
        $deposit->setSummary($data['summary']);
        $deposit->setContentProvider($provider);

        if ($data['uuid'] !== null && $data['uuid'] !== '') {
            $deposit->setUuid($data['uuid']);
        } else {
            $deposit->setUuid(Uuid::v4());
        }

        /** @var SplFileInfo $dataFile */
        
        if ($this->em !== null) {
            $this->em->persist($deposit);
            $this->em->flush();
        }
        return $deposit;
    }
}
