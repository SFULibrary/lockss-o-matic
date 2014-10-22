<?php

/* 
 * The MIT License
 *
 * Copyright (c) 2014 Mark Jordan, mjordan@sfu.ca.
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

namespace LOCKSSOMatic\CRUDBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use LOCKSSOMatic\CRUDBundle\Entity\AuProperties;
use LOCKSSOMatic\CRUDBundle\Form\AuPropertiesType;

/**
 * AuProperties controller.
 *
 */
class AuPropertiesController extends Controller
{

    /**
     * Lists all AuProperties entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('LOCKSSOMaticCRUDBundle:AuProperties')->findAll();

        return $this->render('LOCKSSOMaticCRUDBundle:AuProperties:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new AuProperties entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new AuProperties();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('auproperties_show', array('id' => $entity->getId())));
        }

        return $this->render('LOCKSSOMaticCRUDBundle:AuProperties:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a AuProperties entity.
     *
     * @param AuProperties $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(AuProperties $entity)
    {
        $form = $this->createForm(new AuPropertiesType(), $entity, array(
            'action' => $this->generateUrl('auproperties_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new AuProperties entity.
     *
     */
    public function newAction()
    {
        $entity = new AuProperties();
        $form   = $this->createCreateForm($entity);

        return $this->render('LOCKSSOMaticCRUDBundle:AuProperties:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a AuProperties entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCRUDBundle:AuProperties')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AuProperties entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('LOCKSSOMaticCRUDBundle:AuProperties:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing AuProperties entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCRUDBundle:AuProperties')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AuProperties entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('LOCKSSOMaticCRUDBundle:AuProperties:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a AuProperties entity.
    *
    * @param AuProperties $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(AuProperties $entity)
    {
        $form = $this->createForm(new AuPropertiesType(), $entity, array(
            'action' => $this->generateUrl('auproperties_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing AuProperties entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCRUDBundle:AuProperties')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AuProperties entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('auproperties_edit', array('id' => $id)));
        }

        return $this->render('LOCKSSOMaticCRUDBundle:AuProperties:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a AuProperties entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LOCKSSOMaticCRUDBundle:AuProperties')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AuProperties entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('auproperties'));
    }

    /**
     * Creates a form to delete a AuProperties entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('auproperties_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
