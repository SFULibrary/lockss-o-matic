<?php

namespace LOCKSSOMatic\LockssBundle\Services;

use BeSimple\SoapCommon\Helper;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectManager;
use LOCKSSOMatic\CrudBundle\Entity\Box;
use LOCKSSOMatic\CrudBundle\Entity\Content;
use LOCKSSOMatic\CrudBundle\Service\AuIdGenerator;
use LOCKSSOMatic\LockssBundle\Utilities\LockssSoapClient;
use Monolog\Logger;

/**
 * Symfony Service to download content from a LOCKSS PLN.
 */
class ContentFetcherService
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

    /**
     * @var ContentHasherService
     */
    private $hasher;

    /**
     * Set the logger.
     *
     * @param Logger $logger
     */
    public function setLogger(Logger $logger) {
        $this->logger = $logger;
    }

    /**
     * Set the entity manager from the doctrine registry.
     *
     * @param Registry $registry
     */
    public function setRegistry(Registry $registry) {
        $this->em = $registry->getManager();
    }

    /**
     * Set the AU ID generator.
     *
     * @param AuIdGenerator $idGenerator
     */
    public function setAuIdGenerator(AuIdGenerator $idGenerator) {
        $this->idGenerator = $idGenerator;
    }

    /**
     * Set the content hashher service.
     *
     * @param ContentHasherService $hasher
     */
    public function setHasher(ContentHasherService $hasher) {
        $this->hasher = $hasher;
    }

    /**
     * Download and return one content item from one box. Checks the hash to make sure it's
     * exactly what's expected.
     *
     * @param Content $content
     * @param Box $box
     *
     * @return resource a file handle
     */
    public function download(Content $content, Box $box) {
        $auid = $this->idGenerator->fromContent($content);
        $wsdl = "http://{$box->getHostname()}:{$box->getWebServicePort()}/ws/ContentService?wsdl";
        $fetchClient = new LockssSoapClient();
        $fetchClient->setWsdl($wsdl);
        $fetchClient->setOption('login', $box->getPln()->getUsername());
        $fetchClient->setOption('password', $box->getPln()->getPassword());
        $fetchClient->setOption('attachment_type', Helper::ATTACHMENTS_TYPE_MTOM);

        $fetchResponse = $fetchClient->call('fetchFile', array(
            'auid' => $auid,
            'url' => $content->getUrl(),
        ));

        if(strtoupper(hash($content->getChecksumType(), $fetchResponse->return->dataHandler)) !== strtoupper($content->getChecksumValue())) {
            $this->logger->warning("Download of cached content failed - Downloaded checksum does not match.");
            return;
        }

        $tmpFile = tmpfile();
        fwrite($tmpFile, $fetchResponse->return->dataHandler);
        rewind($tmpFile);

        return $tmpFile;
    }

    /**
     * Fetches a content item from a randomly selected box in the network.
     *
     * @param Content $content
     * @return resource a file handle
     */
    public function fetch(Content $content) {
        $pln = $content->getPln();
        $boxes = $pln->getActiveBoxes()->toArray();
        shuffle($boxes);

        $file = null;

        foreach ($boxes as $box) {
            $hash = $this->hasher->getChecksum($content->getChecksumType(), $content, $box);
            if ($hash !== $content->getChecksumValue()) {
                continue;
            }
            $file = $this->download($content, $box);
            if($file === null) {
                continue;
            }
        }
        if($file === null) {
            $this->logger->error("Cannot find matching content on any LOCKSS box.");
            return;
        }
        return $file;
    }
}
