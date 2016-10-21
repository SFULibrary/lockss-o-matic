<?php

namespace LOCKSSOMatic\LockssBundle\Services;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CrudBundle\Entity\Box;
use LOCKSSOMatic\CrudBundle\Entity\Content;
use LOCKSSOMatic\CrudBundle\Service\AuIdGenerator;
use LOCKSSOMatic\LockssBundle\Utilities\LockssSoapClient;
use Monolog\Logger;

class ContentHasherService
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
     * @var AuIdGenerator
     */
    private $idGenerator;

    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function setRegistry(Registry $registry)
    {
        $this->em = $registry->getManager();
    }

    public function setAuIdGenerator(AuIdGenerator $idGenerator)
    {
        $this->idGenerator = $idGenerator;
    }

    public function getChecksum($type, Content $content, Box $box)
    {
        $pln = $content->getPln();
        $auid = $this->idGenerator->fromContent($content);
        $wsdl = "http://{$box->getHostname()}:{$box->getWebServicePort()}/ws/HasherService?wsdl";

        $hashClient = new LockssSoapClient();
        $hashClient->setWsdl($wsdl);
        $hashClient->setOption('login', $pln->getUsername());
        $hashClient->setOption('password', $pln->getPassword());
        
        $hashResponse = $hashClient->call('hash', array(
            'hasherParams' => array(
                'recordFilterStream' => true,
                'hashType' => 'V3File',
                'algorithm' => $type,
                'url' => $content->getUrl(),
                'auId' => $auid,
            )
        ));
        
        if($hashResponse === null) {
            $this->logger->warning("{$wsdl} failed: " . $hashClient->getErrors());
            return '*';
        }
        
        if( ! property_exists($hashResponse->return, 'blockFileDataHandler')) {
            $this->logger->warning("{$wsdl} returned error: " . $hashResponse->return->errorMessage);
            return '*';
        }
        
        $matches = array();
        if (preg_match("/^([a-fA-F0-9]+)\s+http/m", $hashResponse->return->blockFileDataHandler, $matches)) {
            return $matches[1];
        } 
        return '-';        
    }

}
