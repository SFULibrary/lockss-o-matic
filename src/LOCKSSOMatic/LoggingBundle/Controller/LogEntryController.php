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
     * Lists all LogEntry entities. Uses pagination, 100 entries per page.
     * 
     * @return string rendered template
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
     * @return string rendered template
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

    /**
     * Generate a log entry. Useful for testing, but not much else. Shows the
     * generated log entry.
     * 
     * @return string rendered template
     */
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

    /**
     * Export the log entries as a CSV file for download.
     * 
     * @param Request $request
     * @return StreamedResponse
     */
    public function exportAction(Request $request) {
        $logger = $this->get('activity_log');
        $purge = false;
        if($request->query->get('purge') && $request->query->get('purge') === 'yes') {
            $purge = true;
        }

        $logger->log(
            'Logs exported via HTTP' . ($purge ? ' (purged).' : '.')
        );
        $callback = $logger->exportCallback(true, $purge);
        
        $response = new StreamedResponse();
        $response->headers->set('Content-Type', "text/csv");
        $response->headers->set('Content-Disposition', 'attachment; filename=lockssomatic-activity-log.csv');
        $response->headers->set('Cache-Control', '');
        $response->headers->set('Pragma', "no-cache");
        $response->headers->set('Expires', "0");
        $response->headers->set('Content-Transfer-Encoding', "binary");
        $response->setCallback(function() use ($callback) {
            while(($data = $callback(100)) !== null) {
                echo $data;
                flush();
            }
        });
        return $response;
    }

}
