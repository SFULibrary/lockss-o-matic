<?php

namespace LOCKSSOMatic\CrudBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use LOCKSSOMatic\CrudBundle\Entity\ContentOwner;
use LOCKSSOMatic\CrudBundle\Form\ContentOwnerType;

/**
 * ContentOwner controller. Standard CRUD stuff really.
 *
 * @Route("/contentowner")
 */
class ContentOwnerController extends Controller
{
    /**
     * Lists all ContentOwner entities. Does pagination.
     *
     * @Route("/", name="contentowner")
     * @Method("GET")
     * @Template()
     * 
     * @param Request $request
     * 
     * @return array
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM LOCKSSOMaticCrudBundle:ContentOwner e';
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
     * Creates a new ContentOwner entity.
     *
     * @Route("/", name="contentowner_create")
     * @Method("POST")
     * @Template("LOCKSSOMaticCrudBundle:ContentOwner:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new ContentOwner();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this->addFlash('success', 'The content owner has been saved.');
            return $this->redirect($this->generateUrl('contentowner_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a ContentOwner entity.
     *
     * @param ContentOwner $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ContentOwner $entity)
    {
        $form = $this->createForm(new ContentOwnerType(), $entity, array(
            'action' => $this->generateUrl('contentowner_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new ContentOwner entity.
     *
     * @Route("/new", name="contentowner_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new ContentOwner();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a ContentOwner entity.
     *
     * @Route("/{id}", name="contentowner_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:ContentOwner')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ContentOwner entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing ContentOwner entity.
     *
     * @Route("/{id}/edit", name="contentowner_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:ContentOwner')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ContentOwner entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Creates a form to edit a ContentOwner entity.
     *
     * @param ContentOwner $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ContentOwner $entity)
    {
        $form = $this->createForm(new ContentOwnerType(), $entity, array(
            'action' => $this->generateUrl('contentowner_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing ContentOwner entity.
     *
     * @Route("/{id}", name="contentowner_update")
     * @Method("PUT")
     * @Template("LOCKSSOMaticCrudBundle:ContentOwner:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:ContentOwner')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ContentOwner entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->addFlash('success', 'The content owner has been saved.');
            return $this->redirect($this->generateUrl('contentowner_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a ContentOwner entity.
     *
     * @Route("/{id}/delete", name="contentowner_delete")
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:ContentOwner')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ContentOwner entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('contentowner'));
    }

    /**
     * Creates a form to delete a ContentOwner entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('contentowner_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
