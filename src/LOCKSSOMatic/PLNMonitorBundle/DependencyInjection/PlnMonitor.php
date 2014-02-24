<?php

namespace LOCKSSOMatic\PLNMonitorBundle\DependencyInjection;

use Doctrine\ORM\EntityManager;

use LOCKSSOMatic\CRUDBundle\Entity\Boxes;
use LOCKSSOMatic\CRUDBundle\Entity\BoxStatus;
use LOCKSSOMatic\CRUDBundle\Entity\Plns;
use LOCKSSOMatic\CRUDBundle\Entity\Aus;
use LOCKSSOMatic\CRUDBundle\Entity\AuStatus;

/**
 * Class defining PlnMonitors, which query one or more LOCKSS boxes
 * in a PLN, an AU on a specific box, an AU across all boxes in a PLN.
 *
 * Methods are meant to be run from app/console lockssomatic:monitor
 * via a cronjob.
 *
 * @todo: Write method to query all instances (in PLN) of URL, like
 * $status = $monitor->queryUrl('http://somecontent.someprovider.com/download/foo.zip');
 */


class PlnMonitor
{
    // Number of seconds to pause between queries.
    public $pause;
    
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Queries all boxes in a given PLN (or optionally, a single box)
     * and records the results in the BoxStatus entity.
     * 
     * @param int $plnId
     *   The ID of the PLN to monitor.
     * @param int $boxId
     *   The ID of the box to query.
     */
    public function queryPln($plnId = NULL, $boxId = NULL)
    {
        if (is_null($boxId)) {
            // Query the Pln entity to get the status of its boxes.
            $pln = $this->em->getRepository('LOCKSSOMatic\CRUDBundle\Entity\Plns')
                ->find($plnId);
            $boxes = $pln->getBoxes();
        }
        else {
            $box = $this->em->getRepository('LOCKSSOMatic\CRUDBundle\Entity\Boxes')
                ->find($boxId);
            $boxes = array();
            $boxes[] = $box;
        }

        if (count($boxes)) {
            foreach ($boxes as $box) {
                if (isset($this->pause)) {
                    sleep($this->pause);
                }
                // Set up the SOAP client.
                $client = new \SoapClient($box->getHostname() . "/ws/DaemonStatusService?wsdl",
                    array('login' => $box->getUsername(), 'password' => $box->getPassword()));

                // Check to see if the SOAP API says the daemon is ready.
                $ready = $client->isDaemonReady();
                if ($ready) {
                    // Add an entry to BoxStatus, with no properties.
                    $boxStatus = new BoxStatus();
                    $boxStatus->setBox($box);
                    $this->em->persist($boxStatus);
                    $this->em->flush();
                }
                else {
                    // Add an entry to BoxStatus, with property 'status' and value 'not ready'.                        
                    $boxStatus = new BoxStatus();
                    $boxStatus->setBox($box);
                    $boxStatus->setPropertyKey('Status');
                    $boxStatus->setPropertyValue('Not ready');
                    $this->em->persist($boxStatus);
                    $this->em->flush();
                }
            }
        }
    }

    /**
     * Queries all boxes in a given PLN (or optionally, a single box)and
     * records the results in the BoxStatus entity. Detailed results are
     * only recorded if the box returns anything other than a '100.00% Agreement'
     * status.
     * 
     * Results of the LOCKSS SOAP API's getAuStatus() method return the
     * following:
     * 
     * [return] => stdClass Object
        (
            [accessType] => Subscription
            [availableFromPublisher] => 1
            [contentSize] => 77728494
            [crawlPool] => ca.sfu.lib.plugin.cartoons.SFUCartoonsPlugin
            [creationTime] => 1359092023000
            [currentlyCrawling] => 
            [currentlyPolling] => 
            [diskUsage] => 80576512
            [journalTitle] => Simon Fraser University Library Editorial Cartoons Collection
            [lastCompletedCrawl] => 1392304019386
            [lastCompletedPoll] => 1392434322462
            [lastCrawl] => 1392303119350
            [lastCrawlResult] => Successful
            [lastPoll] => 1392434309993
            [lastPollResult] => Complete
            [pluginName] => Simon Fraser University Library Editorial Cartoons Collection Plugin
            [publisher] => Simon Fraser University
            [repository] => /cache0/gamma/cache/s/
            [status] => 100.00% Agreement
            [substanceState] => Unknown
            [volume] => Simon Fraser University Library Editorial Cartoons Collection: Uluschak, Edd (1970)
        )
        * 
        * @param int $auId
        *   The ID of the AU to monitor.
        * @param int $boxId
        *   The ID of the box to monitor.
     */
    public function queryAu($auId, $boxId = NULL)
    {
        // Query the Au entity to get its PLN ID.
        $au = $this->em->getRepository('LOCKSSOMatic\CRUDBundle\Entity\Aus')
            ->find($auId);

        if (is_null($boxId)) {
            $plnId = $au->getPln()->getId();
            // Query the Pln entity to get the status of its boxes.
            $pln = $this->em->getRepository('LOCKSSOMatic\CRUDBundle\Entity\Plns')
                ->find($plnId);
            $boxes = $pln->getBoxes();
        }
        else {
            $box = $this->em->getRepository('LOCKSSOMatic\CRUDBundle\Entity\Boxes')
                ->find($boxId);
            $boxes = array();
            $boxes[] = $box;
        }

        if (count($boxes)) {
            foreach ($boxes as $box) {
                if (isset($this->pause)) {
                    sleep($this->pause);
                }
                // Set up the SOAP client.
                try {
                    $client = new \SoapClient($box->getHostname() . "/ws/DaemonStatusService?wsdl",
                        array('login' => $box->getUsername(), 'password' => $box->getPassword()));
                    // Check to see if the SOAP API says the daemon is ready.
                    $ready = $client->isDaemonReady();
                }
                catch (SoapFault $e) {
                    // Add an entry to AuStatus, with property 'status' and value 'not ready'
                    // and property 'box.                        
                    $auStatus = new AuStatus();
                    $auStatus->setAu($au);                        
                    $auStatus->setBoxHostname($box->getHostname());
                    $auStatus->setPropertyKey('Status');
                    $auStatus->setPropertyValue($e->faultstring);
                    $this->em->persist($auStatus);
                    $this->em->flush();                    
                }
                // If the daemon is ready, query the box for the AU status.
                if ($ready) {
                    try {
                        // Note: $au->getAuid() gives us the LOCKSS SOAP API auId, not the
                        // Doctrine entity auId.
                        $statusOnBox = $client->getAuStatus(array('auId' => $au->getAuid()));
                        $auStatus = new AuStatus();
                        $auStatus->setAu($au);
                        $auStatus->setBoxHostname($box->getHostname());
                        // If the 'status' property from LOCKSS's getAuStatus() query
                        // is '100.00% Agreement', add an entry documenting the query.
                        if ($statusOnBox->return->status == '100.00% Agreement') {
                            $auStatus = new AuStatus();
                            $auStatus->setAu($au);
                            $auStatus->setBoxHostname($box->getHostname());
                            $this->em->persist($auStatus);
                        }
                        // If not record all the properties for the current AU on the current
                        // box.
                        else {
                            foreach ($statusOnBox->return as $key => $value) {
                                $auStatus = new AuStatus();
                                $auStatus->setAu($au);
                                $auStatus->setBoxHostname($box->getHostname());
                                $auStatus->setPropertyKey($key);
                                $auStatus->setPropertyValue($value);
                                $this->em->persist($auStatus);
                            }
                        }
                        $this->em->flush();
                    }
                    catch (SoapFault $e) {
                        // Add an entry to AuStatus, with property 'status' and value 'not ready'
                        // and property 'box.                        
                        $auStatus = new AuStatus();
                        $auStatus->setAu($au);                        
                        $auStatus->setBoxHostname($box->getHostname());
                        $auStatus->setPropertyKey('Status');
                        $auStatus->setPropertyValue($e->faultstring);
                        $this->em->persist($auStatus);
                        $this->em->flush();
                    }
                }
                else {
                    // Add an entry to AuStatus, with property 'status' and value 'not ready'
                    // and property 'box.                        
                    $auStatus = new AuStatus();
                    $auStatus->setBoxHostname($box->getHostname());
                    $auStatus->setPropertyKey('Status');
                    $auStatus->setPropertyValue('Not ready');
                    $this->em->persist($auStatus);
                    $this->em->flush();
                }
            }
        }
    }

    /**
     * Wrapper method to pass a single-box query off to queryPln().
     * 
     * @param int $plnId
     *   The ID of the PLN to monitor.
     * @param int $boxId
     *   The ID of the box to query.
     */     
    public function queryBox($boxId)
    {
        $this->queryPln(NULL, $boxId);
    }

    /**
     * Wrapper method to pass a single-box query off to queryAu().
     * 
    * @param int $auId
    *   The ID of the AU to monitor.
    * @param int $boxId
    *   The ID of the box to monitor.
     */     
    public function queryAuOnBox($auId, $boxId)
    {
        $this->queryAu($auId, $boxId);
    }
}
