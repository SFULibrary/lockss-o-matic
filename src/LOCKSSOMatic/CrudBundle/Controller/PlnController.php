<?php

namespace LOCKSSOMatic\CrudBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use LOCKSSOMatic\CrudBundle\Entity\Pln;
use LOCKSSOMatic\CrudBundle\Form\PlnType;

/**
 * Pln controller.
 *
 * @Route("/pln")
 */
class PlnController extends Controller
{

    /**
     * Lists all Pln entities.
     *
     * @Route("/", name="pln")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $paginator = $this->get('knp_paginator');
        $entities = $paginator->paginate(
            $em->getRepository('LOCKSSOMaticCrudBundle:Pln')->findAll(),
            $request->query->getInt('page', 1),
            25
        );


        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Pln entity.
     *
     * @Route("/", name="pln_create")
     * @Method("POST")
     * @Template("LOCKSSOMaticCrudBundle:Pln:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Pln();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('pln_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Pln entity.
     *
     * @param Pln $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Pln $entity)
    {
        $form = $this->createForm(new PlnType(), $entity, array(
            'action' => $this->generateUrl('pln_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Pln entity.
     *
     * @Route("/new", name="pln_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Pln();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Pln entity.
     *
     * @Route("/{id}", name="pln_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pln entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Pln entity.
     *
     * @Route("/{id}/edit", name="pln_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pln entity.');
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
    * Creates a form to edit a Pln entity.
    *
    * @param Pln $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Pln $entity)
    {
        $form = $this->createForm(new PlnType(), $entity, array(
            'action' => $this->generateUrl('pln_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Pln entity.
     *
     * @Route("/{id}", name="pln_update")
     * @Method("PUT")
     * @Template("LOCKSSOMaticCrudBundle:Pln:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pln entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('pln_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Pln entity.
     *
     * @Route("/{id}/delete", name="pln_delete")
     */
    public function deleteAction(Request $request, $id)
    {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Pln entity.');
            }

            $em->remove($entity);
            $em->flush();

        return $this->redirect($this->generateUrl('pln'));
    }

    /**
     * Creates a form to delete a Pln entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('pln_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
