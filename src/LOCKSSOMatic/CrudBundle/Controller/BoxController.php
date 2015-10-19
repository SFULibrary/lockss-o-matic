<?php

namespace LOCKSSOMatic\CrudBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use LOCKSSOMatic\CrudBundle\Entity\Box;
use LOCKSSOMatic\CrudBundle\Form\BoxType;

/**
 * Box controller.
 *
 * @Route("/box")
 */
class BoxController extends Controller
{

    /**
     * Lists all Box entities.
     *
     * @Route("/", name="box")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM LOCKSSOMaticCrudBundle:Box e';
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
     * Creates a new Box entity.
     *
     * @Route("/", name="box_create")
     * @Method("POST")
     * @Template("LOCKSSOMaticCrudBundle:Box:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Box();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('box_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Box entity.
     *
     * @param Box $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Box $entity)
    {
        $form = $this->createForm(new BoxType(), $entity, array(
            'action' => $this->generateUrl('box_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Box entity.
     *
     * @Route("/new", name="box_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Box();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Box entity.
     *
     * @Route("/{id}", name="box_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:Box')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Box entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Box entity.
     *
     * @Route("/{id}/edit", name="box_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:Box')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Box entity.');
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
    * Creates a form to edit a Box entity.
    *
    * @param Box $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Box $entity)
    {
        $form = $this->createForm(new BoxType(), $entity, array(
            'action' => $this->generateUrl('box_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Box entity.
     *
     * @Route("/{id}", name="box_update")
     * @Method("PUT")
     * @Template("LOCKSSOMaticCrudBundle:Box:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:Box')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Box entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('box_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Box entity.
     *
     * @Route("/{id}/delete", name="box_delete")
     */
    public function deleteAction(Request $request, $id)
    {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LOCKSSOMaticCrudBundle:Box')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Box entity.');
            }

            $em->remove($entity);
            $em->flush();

        return $this->redirect($this->generateUrl('box'));
    }

    /**
     * Creates a form to delete a Box entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('box_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
