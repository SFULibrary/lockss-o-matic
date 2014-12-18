<?php

namespace LOCKSSOMatic\LoggingBundle\Services;

use Doctrine\ORM\EntityManager;
use LOCKSSOMatic\LoggingBundle\Entity\LogEntry;
use Symfony\Component\HttpFoundation\RequestStack;
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

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(EntityManager $em, SecurityContext $context, RequestStack $requestStack)
    {
        $this->em = $em;
        $this->context = $context;
        $this->requestStack = $requestStack;
    }

    public function log($summary, array $details = array())
    {
        $entry = new LogEntry();
        $trace = debug_backtrace(0, 10);
        $frame = $trace[1];

        # set some defaults.
        $details = array_merge($details, array(
            'level' => 'info',
            'pln' => null,
            'message' => null,
        ));
        
        $entry->setSummary($summary);
        $entry->setLevel($details['level']);
        $entry->setMessage($details['message']);
        $entry->setPln($details['pln']);

        $entry->setClass($frame['class']);
        $entry->setCaller($frame['function']);

        if ($this->context->getToken() && $this->context->getToken()->getUser()) {
            $entry->setUser($this->context->getToken()->getUser());
        } else {
            $entry->setUser('console/' . getenv('USER'));
        }

        $request = $this->requestStack->getCurrentRequest();
        if ($request !== null) {
            $entry->setIp($request->getClientIp());
        } else {
            $entry->setIp('console');
        }

        $this->em->persist($entry);
        $this->em->flush($entry); // only flush the entry.
    }

}
