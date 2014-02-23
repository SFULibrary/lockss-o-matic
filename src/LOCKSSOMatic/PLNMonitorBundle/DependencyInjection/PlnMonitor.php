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
 * Some methods are meant to be run from app/console lockssomatic:monitor
 * via a cronjob.
 * 
 * All methods return an array of status arrays. Each array will have
 * a key (which maps to the 'property_key' in the db and a value
 * (which maps to the 'property_value' in the db).
 *
 * Query all boxes in PLN 2 (returns array of n arrays). For background
 * monitoring of all boxes in a PLN, iterate over all boxes in all PLNs.
 * The app/console command should take a 'pause' parameter to indicate
 * how long, in seconds, to pause between queries.
 * $status = $monitor->queryPln(2);
 *
 * Query box 23 (returns array of 1 array).
 * $status = $monitor->queryBox(23);
 * 
 * Query all instances (in PLN) of AU (returns array of n arrays). For
 * background monitoring of all AUs in a PLN, iterate over all AUs and
 * issue this command. The app/console command should take a 'pause' parameter
 * to indicate how long, in seconds, to pause between queries.
 * $status = $monitor->queryAu(567);
 *
 * Query AU 567 on box 23; returns array of 1 array)).
 * $status = $monitor->queryAuOnBox(567, 23);
 *
 * Query all instances (in PLN) of URL (returns array of n arrays).
 * $status = $monitor->queryUrl('http: * somecontent.someprovider.com/download/foo.zip');
 */


class PlnMonitor
{
    public $boxId;
    public $auId;
    public $plnId;
    
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function queryPln()
    {
        if (isset($this->plnId)) {
            // Query the Pln entity to get the associated boxes.
            $pln = $this->em->getRepository('LOCKSSOMatic\CRUDBundle\Entity\Plns')
                ->find($this->plnId);

            $boxes = $pln->getBoxes();

            if (count($boxes)) {
                foreach ($boxes as $box) {
                    // Set up the SOAP client.
                    $client = new \SoapClient($box->getHostname() . "/ws/DaemonStatusService?wsdl",
                        array('login' => $box->getUsername(), 'password' => $box->getPassword()));

                    // Check to see if the SOAP API says the daemon is ready.
                    $ready = $client->isDaemonReady();
                    if ($ready) {
                        print  "LOCKSS daemon on " . $box->getHostname() . " is responding\n";
                    }
                    else {
                        print "LOCKSS daemon on " . $box->getHostname() . " is not responding\n";
                    }
                }
            }
        }
    }
}
