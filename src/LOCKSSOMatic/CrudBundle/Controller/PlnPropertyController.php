<?php

namespace LOCKSSOMatic\CrudBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use LOCKSSOMatic\CrudBundle\Entity\PlnProperty;
use LOCKSSOMatic\CrudBundle\Form\PlnPropertyType;

/**
 * PlnProperty controller.
 *
 * @Route("/plnproperty")
 */
class PlnPropertyController extends Controller
{

    /**
     * Lists all PlnProperty entities.
     *
     * @Route("/", name="plnproperty")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM LOCKSSOMaticCrudBundle:PlnProperty e';
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
     * Creates a new PlnProperty entity.
     *
     * @Route("/", name="plnproperty_create")
     * @Method("POST")
     * @Template("LOCKSSOMaticCrudBundle:PlnProperty:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new PlnProperty();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('plnproperty_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a PlnProperty entity.
     *
     * @param PlnProperty $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(PlnProperty $entity)
    {
        $form = $this->createForm(new PlnPropertyType(), $entity, array(
            'action' => $this->generateUrl('plnproperty_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new PlnProperty entity.
     *
     * @Route("/new", name="plnproperty_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new PlnProperty();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a PlnProperty entity.
     *
     * @Route("/{id}", name="plnproperty_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:PlnProperty')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PlnProperty entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing PlnProperty entity.
     *
     * @Route("/{id}/edit", name="plnproperty_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:PlnProperty')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PlnProperty entity.');
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
    * Creates a form to edit a PlnProperty entity.
    *
    * @param PlnProperty $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(PlnProperty $entity)
    {
        $form = $this->createForm(new PlnPropertyType(), $entity, array(
            'action' => $this->generateUrl('plnproperty_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing PlnProperty entity.
     *
     * @Route("/{id}", name="plnproperty_update")
     * @Method("PUT")
     * @Template("LOCKSSOMaticCrudBundle:PlnProperty:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:PlnProperty')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PlnProperty entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('plnproperty_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a PlnProperty entity.
     *
     * @Route("/{id}/delete", name="plnproperty_delete")
     */
    public function deleteAction(Request $request, $id)
    {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LOCKSSOMaticCrudBundle:PlnProperty')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find PlnProperty entity.');
            }

            $em->remove($entity);
            $em->flush();

        return $this->redirect($this->generateUrl('plnproperty'));
    }

    /**
     * Creates a form to delete a PlnProperty entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('plnproperty_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
