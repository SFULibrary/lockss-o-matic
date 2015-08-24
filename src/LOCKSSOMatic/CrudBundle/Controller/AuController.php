<?php

namespace LOCKSSOMatic\CrudBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use LOCKSSOMatic\CrudBundle\Entity\Au;
use LOCKSSOMatic\CrudBundle\Form\AuType;

/**
 * Au controller.
 *
 * @Route("/au")
 */
class AuController extends Controller
{

    /**
     * Lists all Au entities.
     *
     * @Route("/", name="au")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $paginator = $this->get('knp_paginator');
        $entities = $paginator->paginate(
            $em->getRepository('LOCKSSOMaticCrudBundle:Au')->findAll(),
            $request->query->getInt('page', 1),
            25
        );


        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Au entity.
     *
     * @Route("/", name="au_create")
     * @Method("POST")
     * @Template("LOCKSSOMaticCrudBundle:Au:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Au();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('au_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Au entity.
     *
     * @param Au $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Au $entity)
    {
        $form = $this->createForm(new AuType(), $entity, array(
            'action' => $this->generateUrl('au_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Au entity.
     *
     * @Route("/new", name="au_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Au();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Au entity.
     *
     * @Route("/{id}", name="au_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:Au')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Au entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Au entity.
     *
     * @Route("/{id}/edit", name="au_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:Au')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Au entity.');
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
    * Creates a form to edit a Au entity.
    *
    * @param Au $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Au $entity)
    {
        $form = $this->createForm(new AuType(), $entity, array(
            'action' => $this->generateUrl('au_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Au entity.
     *
     * @Route("/{id}", name="au_update")
     * @Method("PUT")
     * @Template("LOCKSSOMaticCrudBundle:Au:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:Au')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Au entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('au_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Au entity.
     *
     * @Route("/{id}/delete", name="au_delete")
     */
    public function deleteAction(Request $request, $id)
    {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LOCKSSOMaticCrudBundle:Au')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Au entity.');
            }

            $em->remove($entity);
            $em->flush();

        return $this->redirect($this->generateUrl('au'));
    }

    /**
     * Creates a form to delete a Au entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('au_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
