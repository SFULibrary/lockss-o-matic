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
