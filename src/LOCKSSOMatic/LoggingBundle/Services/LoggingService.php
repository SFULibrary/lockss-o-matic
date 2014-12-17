<?php

namespace LOCKSSOMatic\LoggingBundle\Services;

use Doctrine\ORM\EntityManager;
use LOCKSSOMatic\LoggingBundle\Entity\LogEntry;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\SecurityContext;

class LoggingService
{

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var SecurityContext
     */
    private $context;

    public function __construct(EntityManager $em, SecurityContext $context)
    {
        $this->em = $em;
        $this->context = $context;
    }

    public function log($summary, $level = 'info', $message=null)
    {
        $entry = new LogEntry();
        $trace = debug_backtrace(0, 10);
        $frame = $trace[1];
        
        $entry->setLevel($level);
        $entry->setSummary($summary);

        $entry->setClass($frame['class']);
        $entry->setCaller($frame['function']);
        $entry->setMessage($message);

        if ($this->context->getToken() && $this->context->getToken()->getUser()) {
            $entry->setUser($this->context->getToken()->getUser());
        }
        $this->em->persist($entry);
        $this->em->flush($entry); // only flush the entry.
    }

}
