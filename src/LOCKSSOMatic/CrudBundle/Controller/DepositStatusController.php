<?php

namespace LOCKSSOMatic\CrudBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * DepositStatus controller. All deposit status routes are prefixed
 * by pln/{plnId}/deposit/{depositId}/status
 *
 * @Route("pln/{plnId}/deposit/{depositId}/status")
 */
class DepositStatusController extends ProtectedController
{

    /**
     * Lists all DepositStatus entities for a deposit.
     *
     * @todo add pagination
     *
     * @Route("/", name="depositstatus")
     * @Method("GET")
     * @Template()
     *
     * @param int $plnId
     * @param int $depositId
     *
     * @return array
     */
    public function indexAction($plnId, $depositId) {
        $pln = $this->getPln($plnId);
        $this->requireAccess('MONITOR', $pln);

        $em = $this->getDoctrine()->getManager();
        $deposit = $em->getRepository('LOCKSSOMaticCrudBundle:Deposit')->find($depositId);
        if ($deposit === null) {
            throw $this->createNotFoundException('Unable to find Deposit entity.');
        }
        if ($deposit->getContentProvider()->getPln()->getId() !== $pln->getId()) {
            throw $this->createNotFoundException('The deposit does not exist in this PLN.');
        }

        return array(
            'pln' => $pln,
            'deposit' => $deposit,
        );
    }

    /**
     * Finds and displays a DepositStatus entity for a deposit.
     *
     * @Route("/{id}", name="depositstatus_show")
     * @Method("GET")
     * @Template()
     *
     * @param int $plnId
     * @param int $id
     * @param int $depositId
     *
     * @return array
     */
    public function showAction($plnId, $id, $depositId) {
        $pln = $this->getPln($plnId);
        $this->requireAccess('MONITOR', $pln);
        $em = $this->getDoctrine()->getManager();

        $deposit = $em->getRepository('LOCKSSOMaticCrudBundle:Deposit')->find($depositId);
        if ($deposit === null) {
            throw $this->createNotFoundException('Unable to find Deposit entity.');
        }
        if ($deposit->getContentProvider()->getPln()->getId() !== $pln->getId()) {
            throw $this->createNotFoundException('The deposit does not exist in this PLN.');
        }

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:DepositStatus')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DepositStatus entity.');
        }
        $boxes = array();
        foreach ($em->getRepository('LOCKSSOMaticCrudBundle:Box')->findBy(array('pln' => $entity->getDeposit()->getPln())) as $box) {
            $boxes[$box->getId()] = $box;
        }

        $content = array();
        foreach ($em->getRepository('LOCKSSOMaticCrudBundle:Content')->findBy(array('deposit' => $entity->getDeposit())) as $c) {
            $content[$c->getId()] = $c;
        }

        return array(
            'pln' => $pln,
            'deposit' => $deposit,
            'entity' => $entity,
            'boxes' => $boxes,
            'content' => $content,
        );
    }
}
