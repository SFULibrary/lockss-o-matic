<?php

namespace LOCKSSOMatic\LoggingBundle\Services;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use LOCKSSOMatic\LoggingBundle\Entity\LogEntry;
use ReflectionClass;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\SecurityContext;

// http://www.insanevisions.com/articles/view/symfony-2-activity-log-listener
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
     * @var Request
     */
    private $request;

    /**
     * @var ContainerInterface
     */
    private $container;
    private $ignoredClasses = array(
        'LOCKSSOMatic\LoggingBundle\Entity\LogEntry',
        'LOCKSSOMatic\LoggingBundle\Services\LoggingService',
    );

    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return Request
     */
    private function getRequest()
    {
        if ($this->request === null) {
            $this->request = $this->container->get('request_stack')->getCurrentRequest();
        }
        return $this->request;
    }

    /**
     * @return SecurityContext
     */
    private function getContext()
    {
        if ($this->context === null) {
            $this->context = $this->container->get('security.context');
        }
        return $this->context;
    }

    /**
     * @return EntityManager
     */
    private function getEntityManager()
    {
        if ($this->em === null) {
            $this->em = $this->container->get('doctrine')->getManager();
        }
        return $this->em;
    }

    public function ignoreClass($class)
    {
        $this->ignoredClasses[] = $class;
    }

    private function findStackFrame()
    {
        $trace = debug_backtrace();
        foreach ($trace as $f) {
            if (!array_key_exists('class', $f)) {
                continue;
            }
            $class = $f['class'];
            if (preg_match('/LoggingBundle/', $class) &&
                $class != 'LOCKSSOMatic\LoggingBundle\Command\ExportLogsCommand') {
                continue;
            }
            if (!preg_match('/^LOCKSSOMatic/', $class)) {
                continue;
            }
            return $f;
        }
    }

    public function getUser($details)
    {
        $request = $this->getRequest();
        if ($request && $request->headers) {
            if ($request->headers->has('x-on-behalf-of')) {
                return $request->headers->has('x-on-behalf-of');
            }
            if ($request->headers->has('on-behalf-of')) {
                return $request->headers->has('on-behalf-of');
            }
        }

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
//        $em = $this->getEntityManager();
//        $em->persist($entry);
//        $em->flush($entry); // only flush the entry.
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
            $entity->getId()
            )), 
            $details
        );
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->doctrineLog($args, array(
            'action' => 'created',
            'level' => 'doctrine',
        ));
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->doctrineLog($args, array(
            'action' => 'updated',
            'level' => 'doctrine',
        ));
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $this->doctrineLog($args, array(
            'action' => 'deleted',
            'level' => 'doctrine',
        ));
    }

}
