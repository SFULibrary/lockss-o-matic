<?php

namespace LOCKSSOMatic\CrudBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Content controller. Content is read-only. All routes are prefixed
 * with /pln/{plnId}/content
 *
 * @Route("/pln/{plnId}/content")
 */
class ContentController extends ProtectedController
{
    /**
     * Lists all Content entities for a PLN. Does pagination.
     *
     * @Route("/", name="content")
     * @Method("GET")
     * @Template()
     *
     * @param Request $request
     * @param int $plnId
     *
     * @return array
     */
    public function indexAction(Request $request, $plnId) {
        $pln = $this->getPln($plnId);
        $this->requireAccess('MONITOR', $pln);

        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM LOCKSSOMaticCrudBundle:Content e JOIN e.au a WHERE a.pln = :pln';
        $query = $em->createQuery($dql);
        $query->setParameters(array(
            'pln' => $pln,
        ));
        $paginator = $this->get('knp_paginator');
        $entities = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            25
        );

        return array(
            'pln' => $pln,
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a Content entity. Checks that the
     * $plnId parameter matches the entity's PLN.
     *
     * @Route("/{id}", name="content_show")
     * @Method("GET")
     * @Template()
     *
     * @param int $plnId
     * @param int $id
     *
     * @return array
     */
    public function showAction($plnId, $id) {
        $pln = $this->getPln($plnId);
        $this->requireAccess('MONITOR', $pln);

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:Content')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Content entity.');
        }

        if ($pln->getId() !== $entity->getAu()->getPln()->getId()) {
            $this->addFlash('warning', "This content item is not part of the requested PLN.");
            $this->redirect('content', array('plnId' => $plnId));
        }

        return array(
            'pln' => $pln,
            'entity' => $entity,
        );
    }
}
