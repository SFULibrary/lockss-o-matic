<?php

namespace LOCKSSOMatic\CrudBundle\Controller;

use LOCKSSOMatic\CrudBundle\Entity\DepositStatus;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * DepositStatus controller.
 *
 * @Route("/deposit/{depositId}/status")
 */
class DepositStatusController extends Controller
{

    /**
     * Lists all DepositStatus entities.
     *
     * @Route("/", name="depositstatus")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request, $depositId)
    {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM LOCKSSOMaticCrudBundle:DepositStatus e where e.deposit = :depositId';
        $query = $em->createQuery($dql);
        $query->setParameters(array(
            'depositId' => $depositId,
        ));
        $paginator = $this->get('knp_paginator');
        $entities = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            25
        );


        return array(
            'depositId' => $depositId,
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a DepositStatus entity.
     *
     * @Route("/{id}", name="depositstatus_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id, $depositId)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:DepositStatus')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DepositStatus entity.');
        }
        $boxes = array();
        foreach($em->getRepository('LOCKSSOMaticCrudBundle:Box')->findBy(array('pln' => $entity->getDeposit()->getPln())) as $box) {
            $boxes[$box->getId()] = $box;
        }
        
        $content = array();
        foreach($em->getRepository('LOCKSSOMaticCrudBundle:Content')->findBy(array('deposit' => $entity->getDeposit())) as $c) {
            $content[$c->getId()] = $c;
        }

        return array(
            'depositId'   => $depositId,
            'entity'      => $entity,
            'boxes'       => $boxes,
            'content'     => $content,
        );
    }
}
