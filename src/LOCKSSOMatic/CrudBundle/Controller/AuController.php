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
 * @Route("/au")
 */
class AuController extends ProtectedController {

	/**
	 * Lists all Au entities.
	 *
	 * @Route("/", name="au")
	 * @Method("GET")
	 * @Template()
	 */
	public function indexAction(Request $request) {
		$pln = $this->currentPln();
		if ($pln === null) {
			throw new BadRequestException("You must select a PLN.");
		}
		$this->requireAccess('MONITOR', $pln);

		$em = $this->getDoctrine()->getManager();
		$dql = 'SELECT e FROM LOCKSSOMaticCrudBundle:Au e WHERE e.pln = :pln';
		$query = $em->createQuery($dql);
		$query->setParameters(array(
			'pln' => $pln
		));
		$paginator = $this->get('knp_paginator');
		$entities = $paginator->paginate(
				$query, $request->query->getInt('page', 1), 25
		);


		return array(
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
	public function showAction($id) {
		$em = $this->getDoctrine()->getManager();
		$au = $em->getRepository('LOCKSSOMaticCrudBundle:Au')->find($id);

		$pln = $au->getPln();
		$this->requireAccess('MONITOR', $pln);

		if ($pln !== $this->currentPln()) {
			$this->addFlash('warning', "This AU is part of the {$pln->getName()} PLN, but you have selected {$this->currentPln()} to work with.");
		}

		if (!$au) {
			throw $this->createNotFoundException('Unable to find Au entity.');
		}

		return array(
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
	public function statusAction($id) {
		$em = $this->getDoctrine()->getManager();
		$au = $em->getRepository('LOCKSSOMaticCrudBundle:Au')->find($id);
		return array('entity' => $au);
	}

}
