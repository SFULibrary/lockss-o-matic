<?php

namespace LOCKSSOMatic\CRUDBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Deposits controller.
 *
 */
class DepositsController extends Controller
{

    /**
     * Lists all Deposits entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('LOCKSSOMaticCRUDBundle:Deposits')->findAll();

        return $this->render('LOCKSSOMaticCRUDBundle:Deposits:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Finds and displays a Deposits entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCRUDBundle:Deposits')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Deposits entity.');
        }

        return $this->render('LOCKSSOMaticCRUDBundle:Deposits:show.html.twig', array(
            'entity'      => $entity,
        ));
    }
}
