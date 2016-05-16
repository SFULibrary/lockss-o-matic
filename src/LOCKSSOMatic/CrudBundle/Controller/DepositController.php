<?php

namespace LOCKSSOMatic\CrudBundle\Controller;

use LOCKSSOMatic\SwordBundle\Exceptions\BadRequestException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * Deposit controller.
 *
 * @Route("/pln/{plnId}/deposit")
 */
class DepositController extends ProtectedController 
{

    protected function getPln($plnId) {
        $pln = $this->getDoctrine()->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($plnId);
		if ($pln === null) {
			throw new BadRequestException("Unknown PLN.");
		}
        return $pln;
    }
    
    /**
     * Lists all Deposit entities.
     *
     * @Route("/", name="deposit")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request, $plnId)
    {
		$pln = $this->getPln($plnId);
		$this->requireAccess('MONITOR', $pln);
        
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM LOCKSSOMaticCrudBundle:Deposit e JOIN LOCKSSOMaticCrudBundle:ContentProvider c WHERE c.pln = :pln';
        $query = $em->createQuery($dql);
		$query->setParameters(array(
			'pln' => $pln
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
     * Finds and displays a Deposit entity.
     *
     * @Route("/{id}", name="deposit_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($plnId, $id)
    {
		$pln = $this->getPln($plnId);
		$this->requireAccess('MONITOR', $pln);
        
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:Deposit')->find($id);
        if($entity->getContentProvider()->getPln()->getId() !== $pln->getId()) {
            throw $this->createNotFoundException('The deposit does not exist in this PLN.');
        }

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Deposit entity.');
        }

        return array(
            'entity'      => $entity,
            'pln' => $pln,
        );
    }
}
