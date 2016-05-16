<?php

namespace LOCKSSOMatic\CrudBundle\Controller;

use LOCKSSOMatic\SwordBundle\Exceptions\BadRequestException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Content controller.
 *
 * @Route("/pln/{plnId}/content")
 */
class ContentController extends ProtectedController
{

    protected function getPln($plnId) {
        $pln = $this->getDoctrine()->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($plnId);
		if ($pln === null) {
			throw new BadRequestException("Unknown PLN.");
		}
        return $pln;
    }
    
    /**
     * Lists all Content entities.
     *
     * @Route("/", name="content")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request, $plnId)
    {
		$pln = $this->getPln($plnId);
		$this->requireAccess('MONITOR', $pln);
        
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM LOCKSSOMaticCrudBundle:Content e JOIN e.au a WHERE a.pln = :pln';
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
     * Finds and displays a Content entity.
     *
     * @Route("/{id}", name="content_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($plnId, $id)
    {
		$pln = $this->getPln($plnId);
		$this->requireAccess('MONITOR', $pln);
        
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:Content')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Content entity.');
        }
        
        if($pln->getId() !== $entity->getAu()->getPln()->getId()) {
            $this->addFlash('warning', "This content item is part of the {$pln->getName()} PLN, but you have selected {$this->currentPln()} to work with.");
        }
        
        return array(
            'pln' => $pln,
            'entity'      => $entity,
        );
    }
}
