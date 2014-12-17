<?php

namespace LOCKSSOMatic\LoggingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use LOCKSSOMatic\LoggingBundle\Entity\LogEntry;

/**
 * LogEntry controller.
 *
 */
class LogEntryController extends Controller
{

    /**
     * Lists all LogEntry entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('LOCKSSOMaticLoggingBundle:LogEntry')->findAll();

        return $this->render('LOCKSSOMaticLoggingBundle:LogEntry:index.html.twig',
                array(
                'entities' => $entities,
        ));
    }

    /**
     * Finds and displays a LogEntry entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticLoggingBundle:LogEntry')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find LogEntry entity.');
        }

        return $this->render('LOCKSSOMaticLoggingBundle:LogEntry:show.html.twig',
                array(
                'entity' => $entity,
        ));
    }

    public function generateAction()
    {
        $em = $this->getDoctrine()->getManager();
        $trace = debug_backtrace();
        
        foreach($trace as $caller) {
            $entity = new LogEntry();
            $entity->setCaller($caller['function']);

            if (array_key_exists('class', $caller)) {
                $entity->setClass($caller['class']);
            }

            if (array_key_exists('file', $caller)) {
                $entity->setFile($caller['file']);
            }

            if (array_key_exists('line', $caller)) {
                $entity->setLIne($caller['line']);
            }

            $entity->setUser($this->getUser());
            $entity->setLevel('test');
            $entity->setSummary('Test message');
            $entity->setMessage('This is a simple test message. In the event of a real log entity something interesting would appear here.');

            $em->persist($entity);
        }
        $em->flush();

        return $this->render('LOCKSSOMaticLoggingBundle:LogEntry:show.html.twig',
                array(
                'entity' => $entity,
        ));
    }

}
