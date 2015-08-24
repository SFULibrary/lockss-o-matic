<?php

namespace LOCKSSOMatic\CrudBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use LOCKSSOMatic\CrudBundle\Entity\ExternalTitleDb;
use LOCKSSOMatic\CrudBundle\Form\ExternalTitleDbType;

/**
 * ExternalTitleDb controller.
 *
 * @Route("/externaltitledb")
 */
class ExternalTitleDbController extends Controller
{

    /**
     * Lists all ExternalTitleDb entities.
     *
     * @Route("/", name="externaltitledb")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $paginator = $this->get('knp_paginator');
        $entities = $paginator->paginate(
            $em->getRepository('LOCKSSOMaticCrudBundle:ExternalTitleDb')->findAll(),
            $request->query->getInt('page', 1),
            25
        );


        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new ExternalTitleDb entity.
     *
     * @Route("/", name="externaltitledb_create")
     * @Method("POST")
     * @Template("LOCKSSOMaticCrudBundle:ExternalTitleDb:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new ExternalTitleDb();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('externaltitledb_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a ExternalTitleDb entity.
     *
     * @param ExternalTitleDb $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ExternalTitleDb $entity)
    {
        $form = $this->createForm(new ExternalTitleDbType(), $entity, array(
            'action' => $this->generateUrl('externaltitledb_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new ExternalTitleDb entity.
     *
     * @Route("/new", name="externaltitledb_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new ExternalTitleDb();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a ExternalTitleDb entity.
     *
     * @Route("/{id}", name="externaltitledb_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:ExternalTitleDb')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ExternalTitleDb entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing ExternalTitleDb entity.
     *
     * @Route("/{id}/edit", name="externaltitledb_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:ExternalTitleDb')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ExternalTitleDb entity.');
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
    * Creates a form to edit a ExternalTitleDb entity.
    *
    * @param ExternalTitleDb $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ExternalTitleDb $entity)
    {
        $form = $this->createForm(new ExternalTitleDbType(), $entity, array(
            'action' => $this->generateUrl('externaltitledb_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing ExternalTitleDb entity.
     *
     * @Route("/{id}", name="externaltitledb_update")
     * @Method("PUT")
     * @Template("LOCKSSOMaticCrudBundle:ExternalTitleDb:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:ExternalTitleDb')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ExternalTitleDb entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('externaltitledb_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a ExternalTitleDb entity.
     *
     * @Route("/{id}/delete", name="externaltitledb_delete")
     */
    public function deleteAction(Request $request, $id)
    {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LOCKSSOMaticCrudBundle:ExternalTitleDb')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ExternalTitleDb entity.');
            }

            $em->remove($entity);
            $em->flush();

        return $this->redirect($this->generateUrl('externaltitledb'));
    }

    /**
     * Creates a form to delete a ExternalTitleDb entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('externaltitledb_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
