<?php

namespace LOCKSSOMatic\CrudBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use LOCKSSOMatic\CrudBundle\Entity\ContentProperty;
use LOCKSSOMatic\CrudBundle\Form\ContentPropertyType;

/**
 * ContentProperty controller.
 *
 * @Route("/contentproperty")
 */
class ContentPropertyController extends Controller
{

    /**
     * Lists all ContentProperty entities.
     *
     * @Route("/", name="contentproperty")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM LOCKSSOMaticCrudBundle:ContentProperty e';
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
     * Creates a new ContentProperty entity.
     *
     * @Route("/", name="contentproperty_create")
     * @Method("POST")
     * @Template("LOCKSSOMaticCrudBundle:ContentProperty:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new ContentProperty();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('contentproperty_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a ContentProperty entity.
     *
     * @param ContentProperty $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ContentProperty $entity)
    {
        $form = $this->createForm(new ContentPropertyType(), $entity, array(
            'action' => $this->generateUrl('contentproperty_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new ContentProperty entity.
     *
     * @Route("/new", name="contentproperty_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new ContentProperty();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a ContentProperty entity.
     *
     * @Route("/{id}", name="contentproperty_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:ContentProperty')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ContentProperty entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing ContentProperty entity.
     *
     * @Route("/{id}/edit", name="contentproperty_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:ContentProperty')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ContentProperty entity.');
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
    * Creates a form to edit a ContentProperty entity.
    *
    * @param ContentProperty $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ContentProperty $entity)
    {
        $form = $this->createForm(new ContentPropertyType(), $entity, array(
            'action' => $this->generateUrl('contentproperty_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing ContentProperty entity.
     *
     * @Route("/{id}", name="contentproperty_update")
     * @Method("PUT")
     * @Template("LOCKSSOMaticCrudBundle:ContentProperty:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:ContentProperty')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ContentProperty entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('contentproperty_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a ContentProperty entity.
     *
     * @Route("/{id}/delete", name="contentproperty_delete")
     */
    public function deleteAction(Request $request, $id)
    {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LOCKSSOMaticCrudBundle:ContentProperty')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ContentProperty entity.');
            }

            $em->remove($entity);
            $em->flush();

        return $this->redirect($this->generateUrl('contentproperty'));
    }

    /**
     * Creates a form to delete a ContentProperty entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('contentproperty_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
