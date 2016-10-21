<?php

namespace LOCKSSOMatic\LogBundle\Controller;

use LOCKSSOMatic\LogBundle\Entity\LogEntry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * LogEntry controller.
 *
 * @Route("/log")
 */
class LogEntryController extends Controller
{
    /**
     * Lists all LogEntry entities.
     *
     * @Route("/", name="log")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $paginator = $this->get('knp_paginator');
        $entities = $paginator->paginate(
            $em->getRepository('LOCKSSOMaticLogBundle:LogEntry')->findAll(),
            $request->query->getInt('page', 1),
            25
        );

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a LogEntry entity.
     *
     * @Route("/{id}", name="log_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticLogBundle:LogEntry')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find LogEntry entity.');
        }

        return array(
            'entity' => $entity,
        );
    }
}
