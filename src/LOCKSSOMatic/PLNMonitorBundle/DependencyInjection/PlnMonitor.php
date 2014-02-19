<?php

namespace LOCKSSOMatic\PLNMonitorBundle\DependencyInjection;

/**
 * Class defining PlnMonitors, which query one or more LOCKSS boxes
 * in a PLN, an AU on a specific box, an AU across all boxes in a PLN.
 *
 * Some methods are meant to be run from app/console lockssomatic:monitor
 * via a cronjob.
 */

class PlnMonitor
{
    public $boxId;
    public $auId;
    public $plnId;

    /**
     * Stub for querying box for its status.
     */
    public function displayBoxId()
    {
        if (isset($this->boxId)) {
            return 'From with the Monitor, Box ID is ' . $this->boxId;
        }
    }

    /**
     * Stub for querying a specific AU for its status.
     */
    public function displayAuId()
    {
        if (isset($this->auId)) {
            return 'From within the Monitor, AU ID is ' . $this->auId;
        }
    }

    /**
     * Stub for querying all boxes in a PLN for their status.
     */
    public function displayPlnId()
    {
        if (isset($this->plnId)) {
            return 'From within the Monitor, PLN ID is ' . $this->plnId;
        }
    }
}
