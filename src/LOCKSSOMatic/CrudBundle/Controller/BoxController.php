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

use LOCKSSOMatic\CrudBundle\Entity\Box;
use LOCKSSOMatic\CrudBundle\Form\BoxType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Box controller. Very CRUDish. All routes are prefixed with
 * /pln/{plnId}/box
 *
 * @Route("/pln/{plnId}/box")
 */
class BoxController extends ProtectedController
{
    /**
     * Lists all Box entities for a PLN. Does pagination, although
     * it's unlikely there will be more than 25 boxes in a PLN.
     *
     * @Route("/", name="box")
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
        $dql = 'SELECT e FROM LOCKSSOMaticCrudBundle:Box e WHERE e.pln = :pln';
        $query = $em->createQuery($dql);
        $query->setParameters(array(
            'pln' => $pln,
        ));
        $paginator = $this->get('knp_paginator');
        $entities = $paginator->paginate(
            $query, $request->query->getInt('page', 1), 25
        );

        return array(
            'pln' => $pln,
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Box entity in a PLN.
     *
     * @Route("/", name="box_create")
     * @Method("POST")
     * @Template("LOCKSSOMaticCrudBundle:Box:new.html.twig")
     * 
     * @param Request $request
     * @param int $plnId
     * 
     * @return array|RedirectResponse
     */
    public function createAction(Request $request, $plnId)
    {
        $pln = $this->getPln($plnId);
        $this->requireAccess('MONITOR', $pln);
        $this->requireAccess('PLNADMIN', $pln);

        $entity = new Box();
        $form = $this->createCreateForm($entity, $plnId);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setPln($pln);
            $em = $this->getDoctrine()->getManager();
            $entity->resolveHostname();
            $em->persist($entity);
            $em->flush();

            $this->addFlash('success', "The box has been added to {$pln->getName()}.");

            return $this->redirect($this->generateUrl(
                        'box_show', array(
                        'plnId' => $plnId,
                        'id' => $entity->getId(),
                        )
            ));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Box entity.
     *
     * @param Box $entity The entity
     * @param int $plnId
     *
     * @return Form The form
     */
    private function createCreateForm(Box $entity, $plnId)
    {
        $form = $this->createForm(
            new BoxType(), $entity, array(
            'action' => $this->generateUrl('box_create', array('plnId' => $plnId)),
            'method' => 'POST',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Box entity.
     *
     * @Route("/new", name="box_new")
     * @Method("GET")
     * @Template()
     * 
     * @param int $plnId
     * 
     * @return array
     */
    public function newAction($plnId)
    {
        $pln = $this->getPln($plnId);
        $this->requireAccess('PLNADMIN', $pln);

        $entity = new Box();
        $entity->setPln($pln);
        $form = $this->createCreateForm($entity, $plnId);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'plnId' => $plnId,
        );
    }

    /**
     * Finds and displays a Box entity. Checks that the box is
     * in the PLN.
     *
     * @Route("/{id}", name="box_show")
     * @Method("GET")
     * @Template()
     * 
     * @param int $plnId
     * @param int $id
     * 
     * @return array
     */
    public function showAction($plnId, $id)
    {
        $pln = $this->getPln($plnId);
        $this->requireAccess('MONITOR', $pln);

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:Box')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Box entity.');
        }
        $this->requireAccess('MONITOR', $entity->getPln());

        if ($entity->getPln() !== $pln) {
            $this->addFlash('warning', "This box is part of the {$entity->getPln()->getName()} network, but you have selected the {$pln->getName()} network.");
        }

        $deleteForm = $this->createDeleteForm($id, $plnId);

        return array(
            'pln' => $pln,
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Box entity. Checks that
     * the box is in the PLN.
     *
     * @Route("/{id}/edit", name="box_edit")
     * @Method("GET")
     * @Template()
     * 
     * @param int $plnId
     * @param int $id
     * 
     * @return array|RedirectResponse
     */
    public function editAction($plnId, $id)
    {
        $pln = $this->getPln($plnId);
        $this->requireAccess('PLNADMIN', $pln);

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:Box')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Box entity.');
        }
        if ($entity->getPln() !== $pln) {
            $this->addFlash('danger', "This box is part of the {$entity->getPln()->getName()} network, but you have selected the {$pln->getName()} network.");

            return $this->redirect($this->generateUrl('box', array('id' => $entity->getId(), 'plnId' => $plnId)));
        }

        $editForm = $this->createEditForm($entity, $plnId);
        $deleteForm = $this->createDeleteForm($id, $plnId);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Creates a form to edit a Box entity.
     *
     * @param Box $entity The entity
     * @param int $plnId
     *
     * @return Form The form
     */
    private function createEditForm(Box $entity, $plnId)
    {
        $form = $this->createForm(
            new BoxType(), $entity,
            array(
            'action' => $this->generateUrl(
                'box_update', array(
                'id' => $entity->getId(),
                    'plnId' => $plnId,
                )
            ),
            'method' => 'PUT',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Box entity.
     *
     * @Route("/{id}", name="box_update")
     * @Method("PUT")
     * @Template("LOCKSSOMaticCrudBundle:Box:edit.html.twig")
     * 
     * @param Request $request
     * @param int $plnId
     * @param int $id
     * 
     * @return array|RedirectResponse
     */
    public function updateAction(Request $request, $plnId, $id)
    {
        $pln = $this->getPln($plnId);
        $this->requireAccess('PLNADMIN', $pln);

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:Box')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Box entity.');
        }
        if ($entity->getPln() !== $pln) {
            $this->addFlash('danger', "This box is part of the {$entity->getPln()->getName()} network, but you have selected the {$pln->getName()} network.");

            return $this->redirect($this->generateUrl('box', array('id' => $entity->getId())));
        }

        $deleteForm = $this->createDeleteForm($id, $plnId);
        $editForm = $this->createEditForm($entity, $plnId);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->setPln($pln);
            $entity->resolveHostname();
            $em->flush();

            $this->addFlash('success', 'The box has been updated.');

            return $this->redirect($this->generateUrl('box_show', array(
                        'plnId' => $plnId,
                        'id' => $id,
            )));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Box entity. Does no confirmation checking.
     *
     * @Route("/{id}/delete", name="box_delete")
     * 
     * @param int $plnId
     * @param int $id
     * 
     * @return array|RedirectResponse
     */
    public function deleteAction($plnId, $id)
    {
        $pln = $this->getPln($plnId);
        $this->requireAccess('PLNADMIN', $pln);

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:Box')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Box entity.');
        }

        if ($entity->getPln() !== $pln) {
            $this->addFlash('danger', "This box is part of the {$entity->getPln()->getName()} network, but you have selected the {$pln->getName()} network.");

            return $this->redirect($this->generateUrl('box', array('id' => $entity->getId(), 'plnId' => $plnId)));
        }

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Box entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('box', array('plnId' => $plnId)));
    }

    /**
     * Creates a form to delete a Box entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Form The form
     */
    private function createDeleteForm($id, $plnId)
    {
        return $this->createFormBuilder()
                ->setAction($this->generateUrl('box_delete', array(
                        'id' => $id,
                        'plnId' => $plnId,
                )))
                ->setMethod('DELETE')
                ->add('submit', 'submit', array('label' => 'Delete'))
                ->getForm()
        ;
    }

    /**
     * Displays status entites for an AU.
     * 
     * @param int $id
     * @Route("/{id}/status", name="box_status")
     * @Method("GET")
     * @Template()
     */
    public function statusAction($plnId, $id)
    {
        $pln = $this->getPln($plnId);
        $this->requireAccess('MONITOR', $pln);
        
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:Box')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Box entity.');
        }

        return array('entity' => $entity);
    }
}
