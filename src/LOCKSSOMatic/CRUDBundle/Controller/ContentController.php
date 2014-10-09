<?php

namespace LOCKSSOMatic\CRUDBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Content controller.
 *
 */
class ContentController extends Controller
{

    /**
     * Lists all Content entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('LOCKSSOMaticCRUDBundle:Content')->findAll();

        return $this->render('LOCKSSOMaticCRUDBundle:Content:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Finds and displays a Content entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCRUDBundle:Content')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Content entity.');
        }

        return $this->render('LOCKSSOMaticCRUDBundle:Content:show.html.twig', array(
            'entity'      => $entity,
        ));
    }
}
