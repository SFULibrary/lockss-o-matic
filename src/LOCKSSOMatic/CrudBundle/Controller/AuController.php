<?php

namespace LOCKSSOMatic\CrudBundle\Controller;

use LOCKSSOMatic\SwordBundle\Exceptions\BadRequestException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * Au controller.
 *
 * @Route("/pln/{plnId}/au")
 */
class AuController extends ProtectedController
{
    protected function getPln($plnId)
    {
        $pln = $this->getDoctrine()->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($plnId);
        if ($pln === null) {
            throw $this->createNotFoundException('Unknown PLN.');
        }

        return $pln;
    }

    /**
     * Lists all Au entities.
     *
     * @Route("/", name="au")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request, $plnId)
    {
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
     * Finds and displays a Au entity.
     *
     * @Route("/{id}", name="au_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($plnId, $id)
    {
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
     * Displays status entites for an AU.
     * 
     * @param int $id
     * @Route("/{id}/status", name="au_status")
     * @Method("GET")
     * @Template()
     */
    public function statusAction($plnId, $id)
    {
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
