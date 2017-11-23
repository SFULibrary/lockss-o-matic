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
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Stream\Stream;

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
    public function download(Content $content, Box $box, $username = null, $password = null) {
        $pln = $content->getPln();
        if($pln !== $box->getPln()) {
            $this->logger->error("Cannot download content from a box on a different PLN.");
            return;
        }
        $filepath = tempnam(sys_get_temp_dir(), 'lom-cfs-');
        
        $url = "http://{$box->getHostname()}:{$box->getPln()->getContentPort()}/ServeContent";
        $client = new Client();
        $client->get($url, [
            'query' => ['url' => $content->getUrl()],
            'save_to' => $filepath,
        ]);
        
        $fh = fopen($filepath, 'rb');
        
        $context = hash_init($content->getChecksumType());
        while(($data = fread($fh, 64 * 1024))) {
            hash_update($context, $data);
        }
        $hash = hash_final($context);
        rewind($fh);
        
        if($hash !== $content->getChecksumValue()) {
            $this->logger->warning("Download of cached content failed - Downloaded checksum does not match.");
            return;
        }
        $this->logger->error("Saved to {$filepath}.");
        return $fh;
    }

    /**
     * Fetches a content item from a randomly selected box in the network.
     *
     * @param Content $content
     * @return resource a file handle
     */
    public function fetch(Content $content, $boxId = null, $username = null, $password = null) {
        $pln = $content->getPln();
        $boxes = $pln->getActiveBoxes()->toArray();
        shuffle($boxes);

        $file = null;

        foreach ($boxes as $box) {
            if($boxId && $box->getId() != $boxId) {
                continue;
            }
            $hash = $this->hasher->getChecksum($content->getChecksumType(), $content, $box);
            if (strtolower($hash) !== strtolower($content->getChecksumValue())) {
                $this->logger->warning("Hash mismatch on box {$box->getHostname()} for deposit {$content->getDeposit()->getUuid()}. Expected {$content->getChecksumValue()} got {$hash}.");
                continue;
            }
            $file = $this->download($content, $box, $username, $password);
            if($file !== null) {
                // only need the first one that matches.
                break;
            }
        }
        if($file === null) {
            $this->logger->error("Cannot find matching content on any LOCKSS box.");
            return;
        }
        return $file;
    }
}
