<?php

namespace LOCKSSOMatic\CrudBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use LOCKSSOMatic\CrudBundle\Entity\AuProperty;
use LOCKSSOMatic\CrudBundle\Form\AuPropertyType;

/**
 * AuProperty controller.
 *
 * @Route("/auproperty")
 */
class AuPropertyController extends Controller
{

    /**
     * Lists all AuProperty entities.
     *
     * @Route("/", name="auproperty")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM LOCKSSOMaticCrudBundle:AuProperty e';
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
     * Creates a new AuProperty entity.
     *
     * @Route("/", name="auproperty_create")
     * @Method("POST")
     * @Template("LOCKSSOMaticCrudBundle:AuProperty:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new AuProperty();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('auproperty_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a AuProperty entity.
     *
     * @param AuProperty $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(AuProperty $entity)
    {
        $form = $this->createForm(new AuPropertyType(), $entity, array(
            'action' => $this->generateUrl('auproperty_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new AuProperty entity.
     *
     * @Route("/new", name="auproperty_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new AuProperty();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a AuProperty entity.
     *
     * @Route("/{id}", name="auproperty_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:AuProperty')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AuProperty entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing AuProperty entity.
     *
     * @Route("/{id}/edit", name="auproperty_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:AuProperty')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AuProperty entity.');
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
    * Creates a form to edit a AuProperty entity.
    *
    * @param AuProperty $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(AuProperty $entity)
    {
        $form = $this->createForm(new AuPropertyType(), $entity, array(
            'action' => $this->generateUrl('auproperty_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing AuProperty entity.
     *
     * @Route("/{id}", name="auproperty_update")
     * @Method("PUT")
     * @Template("LOCKSSOMaticCrudBundle:AuProperty:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:AuProperty')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AuProperty entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('auproperty_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a AuProperty entity.
     *
     * @Route("/{id}/delete", name="auproperty_delete")
     */
    public function deleteAction(Request $request, $id)
    {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LOCKSSOMaticCrudBundle:AuProperty')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AuProperty entity.');
            }

            $em->remove($entity);
            $em->flush();

        return $this->redirect($this->generateUrl('auproperty'));
    }

    /**
     * Creates a form to delete a AuProperty entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('auproperty_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
