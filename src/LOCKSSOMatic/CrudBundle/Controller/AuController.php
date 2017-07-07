<?php

namespace LOCKSSOMatic\CrudBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * Au controller. All au routes are prefixed with /pln/{plnId}/au.
 *
 * @Route("/pln/{plnId}/au")
 */
class AuController extends ProtectedController
{
    /**
     * Lists all Au entities for one PLN. Does pagination. Listing
     * AUs for a PLN requires MONITOR access for the PLN.
     *
     * @Route("/", name="au")
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
        $dql = 'SELECT e FROM LOCKSSOMaticCrudBundle:Au e WHERE e.pln = :pln';
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
     * Finds and displays an Au entity - checks that the AU is
     * in the PLN. Viewing an AU requires MONITOR access to the
     * PLN.
     *
     * @Route("/{id}", name="au_show")
     * @Method("GET")
     * @Template()
     *
     * @param int $plnId
     * @param int $id
     *
     * @return array|RedirectResponse
     */
    public function showAction($plnId, $id) {
        $em = $this->getDoctrine()->getManager();
        $au = $em->getRepository('LOCKSSOMaticCrudBundle:Au')->find($id);

        if (!$au) {
            throw $this->createNotFoundException('Unable to find Au entity.');
        }

        $pln = $this->getPln($plnId);
        $this->requireAccess('MONITOR', $pln);

        if ($pln->getId() !== $au->getPln()->getId()) {
            $this->addFlash('warning', 'The PLN does not contain the requested AU.');
            $this->redirect('home');
        }

        return array(
            'pln' => $pln,
            'entity' => $au,
        );
    }

    /**
     * Displays status entites for an AU. Requires MONITOR
     * access for the PLN.
     *
     * @todo add paginated summaries and a details page.
     *
     * @Route("/{id}/status", name="au_status")
     * @Method("GET")
     * @Template()
     *
     * @param int $plnId
     * @param int $id
     *
     * @return array|RedirectResponse
     */
    public function statusAction($plnId, $id) {
        $em = $this->getDoctrine()->getManager();
        $au = $em->getRepository('LOCKSSOMaticCrudBundle:Au')->find($id);

        if (!$au) {
            throw $this->createNotFoundException('Unable to find Au entity.');
        }

        $pln = $this->getPln($plnId);
        $this->requireAccess('MONITOR', $pln);

        if ($pln->getId() !== $au->getPln()->getId()) {
            $this->addFlash('warning', 'The PLN does not contain the requested AU.');
            $this->redirect('home');
        }

        return array(
            'pln' => $pln,
            'entity' => $au,
        );
    }
}
