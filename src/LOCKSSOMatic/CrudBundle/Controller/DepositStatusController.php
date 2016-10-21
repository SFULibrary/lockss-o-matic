<?php

/*
 * The MIT License
 *
 * Copyright 2014-2016. Michael Joyce <ubermichael@gmail.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

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
     * @param Request $request
     * @param int $plnId
     * @param int $depositId
     * 
     * @return array
     */
    public function indexAction(Request $request, $plnId, $depositId)
    {
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
     * @param int $depositId
     * 
     * @return array
     */
    public function showAction($plnId, $id, $depositId)
    {
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
