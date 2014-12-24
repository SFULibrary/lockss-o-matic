<?php

namespace LOCKSSOMatic\LoggingBundle\Controller;

use LOCKSSOMatic\LoggingBundle\Entity\LogEntry;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function exportAction(Request $request) {
        $logger = $this->get('activity_log');
        $purge = false;
        if($request->query->get('purge') && $request->query->get('purge') === 'yes') {
            $purge = true;
        }

        $logger->log(
            'Logs exported via HTTP' . ($purge ? ' (purged).' : '.')
        );
        $handle = $logger->export(true, $purge);
        $response = new StreamedResponse();
        $response->headers->set('Content-Type', "text/csv");
        $response->headers->set('Content-Disposition', 'attachment; filename=lockssomatic-activity-log.csv');
        $response->headers->set('Cache-Control', '');
        $response->headers->set('Pragma', "no-cache");
        $response->headers->set('Expires', "0");
        $response->headers->set('Content-Transfer-Encoding', "binary");
        $response->setCallback(function() use ($handle) {
            while($data = fread($handle, 8192)) {
                echo $data;
                sleep(1);
                flush();
            }
        });
        return $response;
    }

}
