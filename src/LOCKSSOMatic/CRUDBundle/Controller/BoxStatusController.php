<?php

namespace LOCKSSOMatic\CRUDBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use LOCKSSOMatic\CRUDBundle\Entity\BoxStatus;

/**
 * BoxStatus controller.
 *
 */
class BoxStatusController extends Controller
{

    /**
     * Lists all BoxStatus entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('LOCKSSOMaticCRUDBundle:BoxStatus')->findAll();

        return $this->render('LOCKSSOMaticCRUDBundle:BoxStatus:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Finds and displays a BoxStatus entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCRUDBundle:BoxStatus')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BoxStatus entity.');
        }

        return $this->render('LOCKSSOMaticCRUDBundle:BoxStatus:show.html.twig', array(
            'entity'      => $entity,
        ));
    }
}
