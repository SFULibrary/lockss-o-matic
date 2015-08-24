<?php

namespace LOCKSSOMatic\CrudBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use LOCKSSOMatic\CrudBundle\Entity\Plugin;
use LOCKSSOMatic\CrudBundle\Form\PluginType;

/**
 * Plugin controller.
 *
 * @Route("/plugin")
 */
class PluginController extends Controller
{

    /**
     * Lists all Plugin entities.
     *
     * @Route("/", name="plugin")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $paginator = $this->get('knp_paginator');
        $entities = $paginator->paginate(
            $em->getRepository('LOCKSSOMaticCrudBundle:Plugin')->findAll(),
            $request->query->getInt('page', 1),
            25
        );


        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Plugin entity.
     *
     * @Route("/", name="plugin_create")
     * @Method("POST")
     * @Template("LOCKSSOMaticCrudBundle:Plugin:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Plugin();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('plugin_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Plugin entity.
     *
     * @param Plugin $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Plugin $entity)
    {
        $form = $this->createForm(new PluginType(), $entity, array(
            'action' => $this->generateUrl('plugin_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Plugin entity.
     *
     * @Route("/new", name="plugin_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Plugin();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Plugin entity.
     *
     * @Route("/{id}", name="plugin_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:Plugin')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Plugin entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Plugin entity.
     *
     * @Route("/{id}/edit", name="plugin_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:Plugin')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Plugin entity.');
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
    * Creates a form to edit a Plugin entity.
    *
    * @param Plugin $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Plugin $entity)
    {
        $form = $this->createForm(new PluginType(), $entity, array(
            'action' => $this->generateUrl('plugin_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Plugin entity.
     *
     * @Route("/{id}", name="plugin_update")
     * @Method("PUT")
     * @Template("LOCKSSOMaticCrudBundle:Plugin:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:Plugin')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Plugin entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('plugin_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Plugin entity.
     *
     * @Route("/{id}/delete", name="plugin_delete")
     */
    public function deleteAction(Request $request, $id)
    {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LOCKSSOMaticCrudBundle:Plugin')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Plugin entity.');
            }

            $em->remove($entity);
            $em->flush();

        return $this->redirect($this->generateUrl('plugin'));
    }

    /**
     * Creates a form to delete a Plugin entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('plugin_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
