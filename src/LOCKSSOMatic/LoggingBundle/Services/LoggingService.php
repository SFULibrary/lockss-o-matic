<?php

namespace LOCKSSOMatic\LoggingBundle\Services;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use LOCKSSOMatic\LoggingBundle\Entity\LogEntry;
use ReflectionClass;
use Symfony\Component\DependencyInjection\ContainerInterface;
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
    private $request;

    /**
     * @var ContainerInterface
     */
    private $container;
    private $ignoredClasses = array(
        'LOCKSSOMatic\LoggingBundle\Entity\LogEntry'
    );

    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    private function getRequest()
    {
        if ($this->request === null) {
            $this->request = $this->container->get('request_stack')->getCurrentRequest();
        }
    }

    private function getContext()
    {
        if ($this->context === null) {
            $this->context = $this->container->get('security.context');
        }
        return $this->context;
    }

    private function getEntityManager()
    {
        if ($this->em === null) {
            $this->em = $this->container->get('doctrine.orm.entity_manager');
        }
        return $this->em;
    }

    public function ignoreClass($class)
    {
        $this->ignoredClasses[] = $class;
    }

    private function findStackFrame() {
        $trace = debug_backtrace();
        foreach($trace as $f) {
            if( ! array_key_exists('class', $f)) {
                continue;
            }
            $class = $f['class'];
            if(preg_match('/LoggingBundle/', $class) &&
                $class != 'LOCKSSOMatic\LoggingBundle\Command\ExportLogsCommand') {
                continue;
            }
            if( ! preg_match('/^LOCKSSOMatic/', $class)) {
                continue;
            }
            return $f;
        }
    }

    public function getUser($details) {
        $context = $this->getContext();

        if ($context->getToken() && $context->getToken()->getUser()) {
            return $context->getToken()->getUser();
        }
        if (array_key_exists('user', $details)) {
            return $details['user'];
        }
        return 'console/' . getenv('USER');
    }

    public function log($summary, array $details = array())
    {
        $entry = new LogEntry();
        # set some defaults.
        $details = array_merge(array(
            'level' => 'info',
            'pln' => null,
            'message' => null,
            'backtrace' => 1,
        ), $details);

        $frame = $this->findStackFrame();

        $entry->setSummary($summary);
        $entry->setLevel($details['level']);
        $entry->setMessage($details['message']);
        $entry->setPln($details['pln']);
        $entry->setClass($frame['class']);
        $entry->setCaller($frame['function']);

        $entry->setUser($this->getUser($details));

        $request = $this->getRequest();
        if ($request !== null) {
            $entry->setIp($request->getClientIp());
        } else {
            $entry->setIp('console');
        }
        $em = $this->getEntityManager();
        $em->persist($entry);
        $em->flush($entry); // only flush the entry.
    }

    private function doctrineLog(LifecycleEventArgs $args, $details = array())
    {
        $entity = $args->getEntity();
        $class = get_class($entity);
        if (in_array($class, $this->ignoredClasses)) {
            return;
        }
        $reflect = new ReflectionClass($entity);
        $this->log(implode(' ', array(
            'User ',
            $details['action'],
            $reflect->getShortName(),
            $entity
            )),
            $details);
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->doctrineLog($args, array(
            'action' => 'created',
            'backtrace' => 2,
        ));
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->doctrineLog($args, array(
            'action' => 'updated',
            'backtrace' => 4,
            'level' => 'doctrine',
        ));
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $this->doctrineLog($args, array(
            'action' => 'deleted',
            'backtrace' => 2,
        ));
    }

}
