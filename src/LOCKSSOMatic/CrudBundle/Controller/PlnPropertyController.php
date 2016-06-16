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

use LOCKSSOMatic\CrudBundle\Entity\Pln;
use LOCKSSOMatic\CrudBundle\Form\PlnPropertyType;
use LOCKSSOMatic\SwordBundle\Exceptions\BadRequestException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * PlnProperty Controller. All PLN Property routes are
 * prefixed with /pln/{plnId}/property.
 * 
 * @Route("/pln/{plnId}/property")
 */
class PlnPropertyController extends Controller
{
    /**
     * List all PLN properties. Does not do pagination.
     * 
     * @Route("/", name="plnproperty")
     * @Method("GET")
     * @Template()
     * 
     * @param int $plnId
     * 
     * @return array
     */
    public function indexAction($plnId)
    {
        $em = $this->getDoctrine()->getManager();
        $pln = $em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($plnId);
        if ($pln === null) {
            throw new BadRequestException();
        }

        return array(
            'pln' => $pln,
        );
    }

    /**
     * Create a new property.
     * 
     * @Route("/", name="plnproperty_create")
     * @Method("POST")
     * @Template()
     * 
     * @param Request $request
     * @param int     $plnId
     * 
     * @return array|RedirectResponse
     */
    public function createAction(Request $request, $plnId)
    {
        $em = $this->getDoctrine()->getManager();
        $pln = $em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($plnId);
        if ($pln === null) {
            throw new BadRequestException();
        }
        $form = $this->createCreateForm($pln);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            if (count($data['value']) > 1) {
                $pln->setProperty($data['name'], $data['value']);
            } else {
                $pln->setProperty($data['name'], $data['value'][0]);
            }
            $this->addFlash('success', 'The property has been added.');
            $em->flush();

            return $this->redirect($this->generateUrl('plnproperty', array(
                'plnId' => $pln->getId(),
            )));
        }

        return array(
           'pln' => $pln,
           'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a new property for the Pln entity.
     * 
     * @param Pln $pln
     *
     * @return Form the form
     */
    private function createCreateForm(Pln $pln)
    {
        $form = $this->createForm(
            new PlnPropertyType($pln),
            null,
            array(
                'action' => $this->generateUrl('plnproperty_create', array(
                    'plnId' => $pln->getId(),
                    'method' => 'POST',
                )),
            )
        );
        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Creates a form to create a PlnProperty entity for the Pln.
     * 
     * @Route("/new", name="plnproperty_new")
     * @Method("GET")
     * @Template()
     * 
     * @param Request $request
     * @param int     $plnId
     * 
     * @return array
     */
    public function newAction($plnId)
    {
        $em = $this->getDoctrine()->getManager();
        $pln = $em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($plnId);
        if ($pln === null) {
            throw new BadRequestException();
        }
        $form = $this->createCreateForm($pln);

        return array(
            'pln' => $pln,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Pln Property. $id
     * is the property name, usually starting with org.lockss.*
     * 
     * @Route("/{id}/edit", name="plnproperty_edit")
     * @Method("GET")
     * @Template()
     * 
     * @param int    $plnId
     * @param string $id
     * 
     * @return array
     */
    public function editAction($plnId, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $pln = $em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($plnId);
        if ($pln === null) {
            throw new BadRequestException();
        }
        $editForm = $this->createEditForm($pln, $id);

        return array(
            'entity' => $pln,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Creates a form to edit a property.
     * 
     * @param Pln    $pln
     * @param string $id
     *
     * @return Form the form
     */
    private function createEditForm(Pln $pln, $id)
    {
        $form = $this->createForm(
            new PlnPropertyType($pln, $id),
            null,
            array(
                'action' => $this->generateUrl('plnproperty_update', array(
                    'plnId' => $pln->getId(),
                    'id' => $id,
                )),
                'method' => 'PUT',
            )
        );
        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits a PLN property. $id is the name of the property.
     * 
     * @Route("/{id}", name="plnproperty_update")
     * @Method("PUT")
     * @Template("LOCKSSOMaticCrudBundle:PlnProperty:edit.html.twig")
     * 
     * @param Request $request
     * @param int     $plnId
     * @param string  $id
     * 
     * @return array|RedirectResponse
     */
    public function updateAction(Request $request, $plnId, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $pln = $em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($plnId);
        if ($pln === null) {
            throw new BadRequestException();
        }
        $form = $this->createEditForm($pln, $id);
        $form->handleRequest($request);
        $data = $form->getData();
        if ($form->isValid()) {
            $data = $form->getData();
            if (count($data['value']) > 1) {
                $pln->setProperty($data['name'], $data['value']);
            } else {
                $pln->setProperty($data['name'], $data['value'][0]);
            }
            $this->addFlash('success', 'The property has been updated.');
            $em->flush();

            return $this->redirect($this->generateUrl('plnproperty', array(
                'plnId' => $pln->getId(),
            )));
        }
        $this->addFlash('failure', 'The form was not valid.');

        return array(
            'entity' => $pln,
            'edit_form' => $form->createView(),
        );
    }

    /**
     * Deletes a Pln Property. No pfaffing about with
     * confirmation (that's handled by javascript).
     *
     * @Route("/{id}/delete", name="plnproperty_delete")
     * 
     * @param Request $request
     * @param int $plnId
     * @param string $id
     * 
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, $plnId, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $pln = $em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($plnId);
        if ($pln === null) {
            throw new BadRequestException();
        }
        $pln->deleteProperty($id);
        $em->flush();
        $this->addFlash('success', 'The property has been removed.');

        return $this->redirect($this->generateUrl('plnproperty', array(
            'plnId' => $pln->getId(),
        )));
    }
}
