<?php

namespace LOCKSSOMatic\CrudBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use LOCKSSOMatic\CrudBundle\Entity\BoxStatus;
use LOCKSSOMatic\CrudBundle\Form\BoxStatusType;

/**
 * BoxStatus controller.
 *
 * @Route("/boxstatus")
 */
class BoxStatusController extends Controller
{

    /**
     * Lists all BoxStatus entities.
     *
     * @Route("/", name="boxstatus")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM LOCKSSOMaticCrudBundle:BoxStatus e';
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
     * Creates a new BoxStatus entity.
     *
     * @Route("/", name="boxstatus_create")
     * @Method("POST")
     * @Template("LOCKSSOMaticCrudBundle:BoxStatus:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new BoxStatus();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('boxstatus_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a BoxStatus entity.
     *
     * @param BoxStatus $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(BoxStatus $entity)
    {
        $form = $this->createForm(new BoxStatusType(), $entity, array(
            'action' => $this->generateUrl('boxstatus_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new BoxStatus entity.
     *
     * @Route("/new", name="boxstatus_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new BoxStatus();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a BoxStatus entity.
     *
     * @Route("/{id}", name="boxstatus_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:BoxStatus')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BoxStatus entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing BoxStatus entity.
     *
     * @Route("/{id}/edit", name="boxstatus_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:BoxStatus')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BoxStatus entity.');
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
    * Creates a form to edit a BoxStatus entity.
    *
    * @param BoxStatus $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(BoxStatus $entity)
    {
        $form = $this->createForm(new BoxStatusType(), $entity, array(
            'action' => $this->generateUrl('boxstatus_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing BoxStatus entity.
     *
     * @Route("/{id}", name="boxstatus_update")
     * @Method("PUT")
     * @Template("LOCKSSOMaticCrudBundle:BoxStatus:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:BoxStatus')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BoxStatus entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('boxstatus_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a BoxStatus entity.
     *
     * @Route("/{id}/delete", name="boxstatus_delete")
     */
    public function deleteAction(Request $request, $id)
    {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LOCKSSOMaticCrudBundle:BoxStatus')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find BoxStatus entity.');
            }

            $em->remove($entity);
            $em->flush();

        return $this->redirect($this->generateUrl('boxstatus'));
    }

    /**
     * Creates a form to delete a BoxStatus entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('boxstatus_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
