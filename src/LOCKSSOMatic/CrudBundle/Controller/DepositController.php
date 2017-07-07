<?php

namespace LOCKSSOMatic\CrudBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * Deposit controller. Deposit routes are prefixed with /pln/{plnId}/deposit
 * and all require MONITOR access for the PLN.
 *
 * @Route("/pln/{plnId}/deposit")
 */
class DepositController extends ProtectedController
{
    /**
     * Lists all Deposit entities in a PLN. Does pagination.
     *
     * @Route("/", name="deposit")
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
        $qb = $em->getRepository('LOCKSSOMaticCrudBundle:Deposit')->createQueryBuilder('d');
        $qb->select('d')->innerJoin('d.contentProvider', 'p', 'WITH', 'p.pln = :pln');
        $query = $qb->getQuery();
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
     * Search the deposits in a PLN. Paginates the search results.
     *
     * @Route("/search", name="deposit_search")
     * @Method("GET")
     * @Template()
     *
     * @param Request $request
     * @param int $plnId
     *
     * @return array
     */
    public function searchAction(Request $request, $plnId) {
        $pln = $this->getPln($plnId);
        $this->requireAccess('MONITOR', $pln);

        $q = $request->query->get('q', '');
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('LOCKSSOMaticCrudBundle:Deposit');
        $entities = array();
        $results = array();
        if ($q !== '') {
            $results = $repo->search($pln, $q);
            $paginator = $this->get('knp_paginator');
            $entities = $paginator->paginate(
                $results,
                $request->query->getInt('page', 1),
                25
            );
        }

        return array(
            'q' => $q,
            'count' => count($results),
            'pln' => $pln,
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a Deposit entity.
     *
     * @Route("/{id}", name="deposit_show")
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
        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:Deposit')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Deposit entity.');
        }

        if ($entity->getContentProvider()->getPln()->getId() !== $pln->getId()) {
            throw $this->createNotFoundException('The deposit does not exist in this PLN.');
        }

        return array(
            'entity' => $entity,
            'pln' => $pln,
        );
    }
}
