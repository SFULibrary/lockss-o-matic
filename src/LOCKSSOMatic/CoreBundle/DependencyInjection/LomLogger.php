<?php

namespace LOCKSSOMatic\CoreBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;
use Monolog\Logger;

use LOCKSSOMatic\CRUDBundle\Entity\Boxes;
use LOCKSSOMatic\CRUDBundle\Entity\BoxStatus;
use LOCKSSOMatic\CRUDBundle\Entity\Plns;
use LOCKSSOMatic\CRUDBundle\Entity\Aus;
use LOCKSSOMatic\CRUDBundle\Entity\AuStatus;

/**
 * Class defining the LomLogger, which is a wrapper around the Monolog
 * logger.
 */

class LomLogger
{
    // We need to inject the container to use ->get('monolog.logger.lomlogger').
    private $container;
    
    // An instance of the Monolog logger.
    private $logger;
    
    public function __construct(ContainerInterface $container, EntityManager $em, Logger $logger)
    {
        $this->container = $container; 
        $this->em = $em;
        $this->logger = $logger;
    }

    /**
     * Logs a message via the Monolog logger.
     * 
     * @param string $agent
     *   The ID of the PLN to monitor.
     * @param string $event
     *   The ID of the box to query.
     * @param string $outcome
     */
    public function log($agent, $event, $outcome)
    {
        $logger = $this->container->get('monolog.logger.lomlogger');
        $message = "$agent\t$event\t$outcome";
        $logger->info($message);
    }
}
