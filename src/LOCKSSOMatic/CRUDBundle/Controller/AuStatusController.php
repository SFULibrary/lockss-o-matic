<?php

namespace LOCKSSOMatic\CRUDBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use LOCKSSOMatic\CRUDBundle\Entity\AuStatus;
use LOCKSSOMatic\CRUDBundle\Form\AuStatusType;

/**
 * AuStatus controller.
 *
 */
class AuStatusController extends Controller
{

    /**
     * Lists all AuStatus entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('LOCKSSOMaticCRUDBundle:AuStatus')->findAll();

        return $this->render('LOCKSSOMaticCRUDBundle:AuStatus:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Finds and displays a AuStatus entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCRUDBundle:AuStatus')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AuStatus entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('LOCKSSOMaticCRUDBundle:AuStatus:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

}
