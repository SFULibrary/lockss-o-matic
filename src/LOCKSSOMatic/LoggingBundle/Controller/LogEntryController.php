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
        $logger = $this->get('activity_log');
        $logger->log('test the service.');
        
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LOCKSSOMaticLoggingBundle:LogEntry')
            ->findOneBy(array(), array('id' => 'DESC'), 1);
        
        return $this->render('LOCKSSOMaticLoggingBundle:LogEntry:show.html.twig',
                array(
                'entity' => $entity,
        ));
    }

}
