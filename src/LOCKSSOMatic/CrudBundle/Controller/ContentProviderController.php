<?php

namespace LOCKSSOMatic\CrudBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use LOCKSSOMatic\CrudBundle\Entity\ContentProvider;
use LOCKSSOMatic\CrudBundle\Form\ContentProviderType;

/**
 * ContentProvider controller.
 *
 * @Route("/contentprovider")
 */
class ContentProviderController extends Controller
{

    /**
     * Lists all ContentProvider entities.
     *
     * @Route("/", name="contentprovider")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $paginator = $this->get('knp_paginator');
        $entities = $paginator->paginate(
            $em->getRepository('LOCKSSOMaticCrudBundle:ContentProvider')->findAll(),
            $request->query->getInt('page', 1),
            25
        );


        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new ContentProvider entity.
     *
     * @Route("/", name="contentprovider_create")
     * @Method("POST")
     * @Template("LOCKSSOMaticCrudBundle:ContentProvider:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new ContentProvider();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('contentprovider_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a ContentProvider entity.
     *
     * @param ContentProvider $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ContentProvider $entity)
    {
        $form = $this->createForm(new ContentProviderType(), $entity, array(
            'action' => $this->generateUrl('contentprovider_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new ContentProvider entity.
     *
     * @Route("/new", name="contentprovider_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new ContentProvider();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a ContentProvider entity.
     *
     * @Route("/{id}", name="contentprovider_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:ContentProvider')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ContentProvider entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing ContentProvider entity.
     *
     * @Route("/{id}/edit", name="contentprovider_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:ContentProvider')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ContentProvider entity.');
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
    * Creates a form to edit a ContentProvider entity.
    *
    * @param ContentProvider $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ContentProvider $entity)
    {
        $form = $this->createForm(new ContentProviderType(), $entity, array(
            'action' => $this->generateUrl('contentprovider_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing ContentProvider entity.
     *
     * @Route("/{id}", name="contentprovider_update")
     * @Method("PUT")
     * @Template("LOCKSSOMaticCrudBundle:ContentProvider:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:ContentProvider')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ContentProvider entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('contentprovider_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a ContentProvider entity.
     *
     * @Route("/{id}/delete", name="contentprovider_delete")
     */
    public function deleteAction(Request $request, $id)
    {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LOCKSSOMaticCrudBundle:ContentProvider')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ContentProvider entity.');
            }

            $em->remove($entity);
            $em->flush();

        return $this->redirect($this->generateUrl('contentprovider'));
    }

    /**
     * Creates a form to delete a ContentProvider entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('contentprovider_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
