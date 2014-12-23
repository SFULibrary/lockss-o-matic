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
        $dql = 'SELECT p FROM LOCKSSOMaticLoggingBundle:LogEntry p ORDER BY p.id DESC';
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            100
        );

        return $this->render('LOCKSSOMaticLoggingBundle:LogEntry:index.html.twig', array(
            'pagination' => $pagination,
        ));
    }

    /**
     * Finds and displays a LogEntry entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var LogEntry */
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
