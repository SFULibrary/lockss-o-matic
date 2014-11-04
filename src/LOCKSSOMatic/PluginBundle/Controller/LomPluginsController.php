<?php

namespace LOCKSSOMatic\PluginBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use LOCKSSOMatic\PluginBundle\Entity\LomPlugins;
use LOCKSSOMatic\PluginBundle\Form\LomPluginsType;

/**
 * LomPlugins controller.
 *
 */
class LomPluginsController extends Controller
{

    /**
     * Lists all LomPlugins entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('LOCKSSOMaticPluginBundle:LomPlugins')->findAll();

        return $this->render('LOCKSSOMaticPluginBundle:LomPlugins:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new LomPlugins entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new LomPlugins();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('lomplugins_show', array('id' => $entity->getId())));
        }

        return $this->render('LOCKSSOMaticPluginBundle:LomPlugins:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a LomPlugins entity.
     *
     * @param LomPlugins $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(LomPlugins $entity)
    {
        $form = $this->createForm(new LomPluginsType(), $entity, array(
            'action' => $this->generateUrl('lomplugins_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new LomPlugins entity.
     *
     */
    public function newAction()
    {
        $entity = new LomPlugins();
        $form   = $this->createCreateForm($entity);

        return $this->render('LOCKSSOMaticPluginBundle:LomPlugins:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a LomPlugins entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticPluginBundle:LomPlugins')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find LomPlugins entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('LOCKSSOMaticPluginBundle:LomPlugins:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing LomPlugins entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticPluginBundle:LomPlugins')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find LomPlugins entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('LOCKSSOMaticPluginBundle:LomPlugins:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a LomPlugins entity.
    *
    * @param LomPlugins $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(LomPlugins $entity)
    {
        $form = $this->createForm(new LomPluginsType(), $entity, array(
            'action' => $this->generateUrl('lomplugins_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing LomPlugins entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticPluginBundle:LomPlugins')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find LomPlugins entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('lomplugins_edit', array('id' => $id)));
        }

        return $this->render('LOCKSSOMaticPluginBundle:LomPlugins:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a LomPlugins entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LOCKSSOMaticPluginBundle:LomPlugins')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find LomPlugins entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('lomplugins'));
    }

    /**
     * Creates a form to delete a LomPlugins entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lomplugins_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
