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
use LOCKSSOMatic\CrudBundle\Entity\ContentProvider;
use LOCKSSOMatic\CrudBundle\Form\ContentProviderType;

/**
 * ContentProvider controller.
 *
 * @Route("/contentprovider")
 */
class ContentProviderController extends Controller
{

    /**
     * Lists all ContentProvider entities.
     *
     * @Route("/", name="contentprovider")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM LOCKSSOMaticCrudBundle:ContentProvider e';
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
     * Creates a new ContentProvider entity.
     *
     * @Route("/", name="contentprovider_create")
     * @Method("POST")
     * @Template("LOCKSSOMaticCrudBundle:ContentProvider:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new ContentProvider();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('contentprovider_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a ContentProvider entity.
     *
     * @param ContentProvider $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ContentProvider $entity)
    {
        $form = $this->createForm(new ContentProviderType(), $entity, array(
            'action' => $this->generateUrl('contentprovider_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new ContentProvider entity.
     *
     * @Route("/new", name="contentprovider_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new ContentProvider();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a ContentProvider entity.
     *
     * @Route("/{id}", name="contentprovider_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:ContentProvider')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ContentProvider entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing ContentProvider entity.
     *
     * @Route("/{id}/edit", name="contentprovider_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:ContentProvider')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ContentProvider entity.');
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
    * Creates a form to edit a ContentProvider entity.
    *
    * @param ContentProvider $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ContentProvider $entity)
    {
        $form = $this->createForm(new ContentProviderType(), $entity, array(
            'action' => $this->generateUrl('contentprovider_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing ContentProvider entity.
     *
     * @Route("/{id}", name="contentprovider_update")
     * @Method("PUT")
     * @Template("LOCKSSOMaticCrudBundle:ContentProvider:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:ContentProvider')->find($id);
        $this->container->get('logger')->error("UUID IS " . $request->request->get('lockssomatic_crudbundle_contentprovider[uuid]'));
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ContentProvider entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('contentprovider_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a ContentProvider entity.
     *
     * @Route("/{id}/delete", name="contentprovider_delete")
     */
    public function deleteAction(Request $request, $id)
    {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LOCKSSOMaticCrudBundle:ContentProvider')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ContentProvider entity.');
            }

            $em->remove($entity);
            $em->flush();

        return $this->redirect($this->generateUrl('contentprovider'));
    }

    /**
     * Creates a form to delete a ContentProvider entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('contentprovider_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    private function createImportForm() {
        $formBuilder = $this->createFormBuilder();
        $formBuilder->add('uuid', 'text', array(
            'label' => 'Deposit UUID',
            'required' => false
            ));
        $formBuilder->add('title', 'text');
        $formBuilder->add('summary', 'textarea');
        $formBuilder->add('file', 'file', array('label' => 'CSV File'));
        $formBuilder->add('submit', 'submit', array('label' => 'Import'));
        return $formBuilder->getForm();
    }

    /**
     * Import a CSV file.
     *
     * @param Request $request
     * @Route("/{id}/csv")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function csvAction(Request $request) {
        $form = $this->createImportForm();
        $form->handleRequest($request);

        if($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $builder = new DepositBuilder();
            $deposit = $builder->fromForm($form, $em);
            return $this->redirect($this->generateUrl('deposit_show', array('id' => $deposit->getId())));
        }
        return array(
            'form' => $form->createView(),
        );

    }
    
}
