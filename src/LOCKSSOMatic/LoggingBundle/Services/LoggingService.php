<?php

/* 
 * The MIT License
 *
 * Copyright (c) 2014 Mark Jordan, mjordan@sfu.ca.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace LOCKSSOMatic\LoggingBundle\Services;

use Doctrine\Common\EventArgs;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Exception;
use LOCKSSOMatic\LoggingBundle\Entity\LogEntry;
use ReflectionClass;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;


/**
 * Symfony logging service. Automatically logs CRUD operations, and can optionally
 * log other events as needed.
 * 
 * @see http://www.insanevisions.com/articles/view/symfony-2-activity-log-listener
 */
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
    
    /**
     * Is the logger enabled?
     * 
     * @var boolean
     */
    private $enabled = true;
    
    /**
     * Array of class names (including namespaces) to ignore in the logger, in
     * order to prevent infinite recursion.
     *
     * @var type 
     */
    private $ignoredClasses = array(
        'LOCKSSOMatic\LoggingBundle\Entity\LogEntry',
        'LOCKSSOMatic\LoggingBundle\Services\LoggingService',
    );
    
    /**
     * Overriden user name. 
     *
     * @var type 
     */
    private $userOverride;

    /**
     * Set the Symfony container. Called automatically.
     * 
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
        if ($container->hasParameter('activity_log.enabled')) {
            $this->enabled = $container->getParameter('activity_log.enabled');
        }
    }

    /**
     * Enable the activity log.
     */
    public function enable()
    {
        $this->enabled = true;
    }

    /**
     * Disable the activity log.
     */
    public function disable()
    {
        $this->enabled = false;
    }

    /**
     * Get the HTTP request being processed, if there is one.
     * 
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
     * Get the security context of the curret request, if there is one.
     * 
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
     * Get the doctrine entity manager.
     * 
     * @return EntityManager
     */
    private function getEntityManager()
    {
        if ($this->em === null) {
            $this->em = $this->container->get('doctrine')->getManager();
        }
        return $this->em;
    }

    /**
     * Add a class name to the list of ignored classes. Include the namespace.
     * 
     * @todo Allow objects to be passed here, and get the class name from them.
     * 
     * @param string $class
     */
    public function ignoreClass($class)
    {
        $this->ignoredClasses[] = $class;
    }

    /**
     * Walk up the stack frame to find an appropriate caller for logging. Symfony
     * gets in the way quite a bit here, so try to find the first LOCKSSOMatic
     * class that isn't LoggingBundle.
     * 
     * @return type
     */
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

    /**
     * The SWORD bundle doesn't use Symfony users in the normal way. User IDs
     * are really UUIDs passed as HTTP headers or query parameters. 
     * overrideUser() lets code specify the user more than just "anon."
     * 
     * @param type $user
     */
    public function overrideUser($user)
    {
        $this->userOverride = $user;
    }

    /**
     * Get the user name. 
     * 
     * If #overrideUser() was called, then that name is returned. 
     * 
     * If there is a security context, then the user name from the context is 
     * returned.
     * 
     * If the $details array includes a user key, then it is returned.
     * 
     * Otherwise, return 'console/' + the shell/command line user name.
     * 
     * @param array $details
     * @return string
     */
    public function getUser(array $details = array())
    {
        if ($this->userOverride !== null) {
            return $this->userOverride;
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

    /**
     * Create and store one log message
     * 
     * The second parameter is an array of message details with keys 
     *   level, a message level, defaults to info
     *   pln, the PLN name, defaults to null
     *   message, the detailed message, defaults to null,
     * 
     * @param string $summary a brief message
     * @param array $details array with message details.
     */
    public function log($summary, array $details = array())
    {
        if ($this->enabled === false) {
            return;
        }
        $entry = new LogEntry();
        # set some defaults.
        $details = array_merge(array(
            'level'     => 'info',
            'pln'       => null,
            'message'   => null,
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

    /**
     * Log a doctrine event.
     * 
     * @param EventArgs $args
     * @param array $details
     * @throws Exception
     */
    private function doctrineLog(EventArgs $args, $details = array())
    {
        if ($args instanceof LifecycleEventArgs) {
            $entity = $args->getEntity();
            $id = $entity->getId();
        } else if ($args instanceof PostFlushEventArgs) {
            $entity = $details['entity'];
            $id = $details['id'];
        } else {
            throw new Exception('Unknown class ' . get_class($args));
        }

        $class = get_class($entity);
        if (in_array($class, $this->ignoredClasses)) {
            return;
        }
        $reflect = new ReflectionClass($entity);
        $this->log(implode(' ',
                array(
            'User ',
            $details['action'],
            $reflect->getShortName(),
            $id,
            )), $details
        );
    }

    /**
     * Called automatically after an entity has been persisted.
     * 
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->doctrineLog($args,
            array(
            'action' => 'created',
            'level'  => 'doctrine',
        ));
    }

    /**
     * Called automatically after an entity has been updated.
     * 
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->doctrineLog($args,
            array(
            'action' => 'updated',
            'level'  => 'doctrine',
        ));
    }

    /**
     * Called *before* an entity has been scheduled for removal, but before it 
     * has has been flushed. Doctrine doesn't let preRemove() events update
     * the database (something about infinite recursion hooey). The log is 
     * actually created in postFlush(), this makes note of the entity in the
     * session.
     * 
     * @param LifecycleEventArgs $args
     * @return type
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $class = get_class($entity);
        if (in_array($class, $this->ignoredClasses)) {
            return;
        }
        $this->container->get('session')->set('entity_removed',
            array(
            'entity' => $entity,
            'id'     => $entity->getId(),
        ));
    }

    /**
     * After the entity manager is flushed, this function is called. It checks
     * for a removed entity, and creates a log of its removal.
     * 
     * @param PostFlushEventArgs $args
     * @return boolean
     */
    public function postFlush(PostFlushEventArgs $args)
    {
        $removed = $this->container->get('session')->get('entity_removed');
        if ($removed === null) {
            return true;
        }
        $id = $removed['id'];
        $entity = $removed['entity'];
        // The log() function flushes. Flushing will call this event handler.
        // So make sure the stored entity is nulled.
        $this->container->get('session')->set('entity_removed', null);
        $class = get_class($entity);
        if (in_array($class, $this->ignoredClasses)) {
            return true;
        }

        $this->doctrineLog($args,
            array(
            'action' => 'deleted',
            'level'  => 'doctrine',
            'entity' => $entity,
            'id'     => $id,
        ));
        return true;
    }

    /**
     * Export the logs. Returns a temporary file handle which can be read to
     * get the logs and save them on disk, return them to a browser, or
     * whatever.
     * 
     * @param boolean $header include the CSV header
     * @param boolean $purge remove the exported log entries
     * @return resource
     */
    public function export($header = true, $purge = false)
    {
        $em = $this->getEntityManager();
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        $dql = 'SELECT p FROM LOCKSSOMaticLoggingBundle:LogEntry p';
        $query = $em->createQuery($dql);
        $iterator = $query->iterate();

        $count = 0;
        $mb = 1024 * 1024;
        $handle = fopen("php://temp/maxmemory:{$mb}", 'rw');
        if ($header) {
            fputcsv($handle, LogEntry::toArrayHeader());
        }
        while ($row = $iterator->next()) {
            $entry = $row[0];
            fputcsv($handle, $entry->toArray());
            if ($purge) {
                $em->remove($entry);
                $em->flush($entry);
            }
            $em->clear();
            $count++;
        }
        if ($purge) {
            $em->flush();
            $this->log($count . ' log entries purged from the database.');
        }
        rewind($handle);
        return $handle;
    }

    /**
     * Create a callback function that streams the log entries. Especially
     * useful if there are many log entries to return.
     * 
     * $callback = $activityLog->exportCallback();
     * while($data = $callback(100)) {
     *   // do stuff with the log data, contains 100 entries in CSV.
     * }
     * 
     * @param boolean $header include the CSV header
     * @param boolean $purge remove the exported log entries
     * @return callback
     */
    public function exportCallback($header = true, $purge = false)
    {
        $em = $this->getEntityManager();
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        $dql = 'SELECT p FROM LOCKSSOMaticLoggingBundle:LogEntry p';
        $query = $em->createQuery($dql);
        $iterator = $query->iterate();
        $finished = false;
        
        $iterator->next(); // get the iterator started.

        $callback = function($n = 100) use($em, $iterator, $finished) {
            if ($finished) {
                return null;
            }
            // reset the execution time limit - this can take a long while,
            // and even though it is constantly returning data to the browser
            // Apache will kill it.
            set_time_limit(30);
            $handle = fopen('php://temp/memory:' . 1024 * 1024, 'w+');
            $i = 0;
            while ($i < $n && $iterator->valid() && $row = $iterator->current()) {
                $entry = $row[0];
                fputcsv($handle, $entry->toArray());
                $i++;
                $em->detach($entry);
                $iterator->next();
            }
            $em->clear();
            if (($i !== $n) || ($row === false)) {
                $finished = true;
            }
            if ($i === 0) {
                return null;
            }
            fflush($handle);
            rewind($handle);
            $content = stream_get_contents($handle);
            fclose($handle);
            return $content;
        };

        return $callback;
    }

}
