<?php

namespace LOCKSSOMatic\CrudBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use LOCKSSOMatic\CrudBundle\Entity\AuStatus;
use LOCKSSOMatic\CrudBundle\Form\AuStatusType;

/**
 * AuStatus controller.
 *
 * @Route("/austatus")
 */
class AuStatusController extends Controller
{

    /**
     * Lists all AuStatus entities.
     *
     * @Route("/", name="austatus")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM LOCKSSOMaticCrudBundle:AuStatus e';
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
     * Creates a new AuStatus entity.
     *
     * @Route("/", name="austatus_create")
     * @Method("POST")
     * @Template("LOCKSSOMaticCrudBundle:AuStatus:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new AuStatus();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('austatus_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a AuStatus entity.
     *
     * @param AuStatus $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(AuStatus $entity)
    {
        $form = $this->createForm(new AuStatusType(), $entity, array(
            'action' => $this->generateUrl('austatus_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new AuStatus entity.
     *
     * @Route("/new", name="austatus_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new AuStatus();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a AuStatus entity.
     *
     * @Route("/{id}", name="austatus_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:AuStatus')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AuStatus entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing AuStatus entity.
     *
     * @Route("/{id}/edit", name="austatus_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:AuStatus')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AuStatus entity.');
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
    * Creates a form to edit a AuStatus entity.
    *
    * @param AuStatus $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(AuStatus $entity)
    {
        $form = $this->createForm(new AuStatusType(), $entity, array(
            'action' => $this->generateUrl('austatus_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing AuStatus entity.
     *
     * @Route("/{id}", name="austatus_update")
     * @Method("PUT")
     * @Template("LOCKSSOMaticCrudBundle:AuStatus:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:AuStatus')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AuStatus entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('austatus_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a AuStatus entity.
     *
     * @Route("/{id}/delete", name="austatus_delete")
     */
    public function deleteAction(Request $request, $id)
    {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LOCKSSOMaticCrudBundle:AuStatus')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AuStatus entity.');
            }

            $em->remove($entity);
            $em->flush();

        return $this->redirect($this->generateUrl('austatus'));
    }

    /**
     * Creates a form to delete a AuStatus entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('austatus_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
