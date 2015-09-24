<?php

/*
 * The MIT License
 *
 * Copyright 2014. Michael Joyce <ubermichael@gmail.com>.
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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use LOCKSSOMatic\CrudBundle\Entity\Content;
use LOCKSSOMatic\CrudBundle\Form\ContentType;

/**
 * Content controller.
 *
 * @Route("/content")
 */
class ContentController extends Controller
{

    /**
     * Lists all Content entities.
     *
     * @Route("/", name="content")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM LOCKSSOMaticCrudBundle:Content e';
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $entities = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            25
        );


        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Content entity.
     *
     * @Route("/", name="content_create")
     * @Method("POST")
     * @Template("LOCKSSOMaticCrudBundle:Content:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Content();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('content_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Content entity.
     *
     * @param Content $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Content $entity)
    {
        $form = $this->createForm(new ContentType(), $entity, array(
            'action' => $this->generateUrl('content_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Content entity.
     *
     * @Route("/new", name="content_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Content();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Content entity.
     *
     * @Route("/{id}", name="content_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:Content')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Content entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Content entity.
     *
     * @Route("/{id}/edit", name="content_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:Content')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Content entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Content entity.
    *
    * @param Content $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Content $entity)
    {
        $form = $this->createForm(new ContentType(), $entity, array(
            'action' => $this->generateUrl('content_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Content entity.
     *
     * @Route("/{id}", name="content_update")
     * @Method("PUT")
     * @Template("LOCKSSOMaticCrudBundle:Content:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:Content')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Content entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('content_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Content entity.
     *
     * @Route("/{id}/delete", name="content_delete")
     */
    public function deleteAction(Request $request, $id)
    {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LOCKSSOMaticCrudBundle:Content')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Content entity.');
            }

            $em->remove($entity);
            $em->flush();

        return $this->redirect($this->generateUrl('content'));
    }

    /**
     * Creates a form to delete a Content entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('content_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
