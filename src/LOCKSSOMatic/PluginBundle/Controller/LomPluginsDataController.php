<?php

namespace LOCKSSOMatic\PluginBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use LOCKSSOMatic\PluginBundle\Entity\LomPluginsData;
use LOCKSSOMatic\PluginBundle\Form\LomPluginsDataType;

/**
 * LomPluginsData controller.
 *
 */
class LomPluginsDataController extends Controller
{

    /**
     * Lists all LomPluginsData entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('LOCKSSOMaticPluginBundle:LomPluginsData')->findAll();

        return $this->render('LOCKSSOMaticPluginBundle:LomPluginsData:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new LomPluginsData entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new LomPluginsData();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('lompluginsdata_show', array('id' => $entity->getId())));
        }

        return $this->render('LOCKSSOMaticPluginBundle:LomPluginsData:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a LomPluginsData entity.
     *
     * @param LomPluginsData $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(LomPluginsData $entity)
    {
        $form = $this->createForm(new LomPluginsDataType(), $entity, array(
            'action' => $this->generateUrl('lompluginsdata_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new LomPluginsData entity.
     *
     */
    public function newAction()
    {
        $entity = new LomPluginsData();
        $form   = $this->createCreateForm($entity);

        return $this->render('LOCKSSOMaticPluginBundle:LomPluginsData:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a LomPluginsData entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticPluginBundle:LomPluginsData')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find LomPluginsData entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('LOCKSSOMaticPluginBundle:LomPluginsData:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing LomPluginsData entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticPluginBundle:LomPluginsData')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find LomPluginsData entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('LOCKSSOMaticPluginBundle:LomPluginsData:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a LomPluginsData entity.
    *
    * @param LomPluginsData $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(LomPluginsData $entity)
    {
        $form = $this->createForm(new LomPluginsDataType(), $entity, array(
            'action' => $this->generateUrl('lompluginsdata_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing LomPluginsData entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticPluginBundle:LomPluginsData')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find LomPluginsData entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('lompluginsdata_edit', array('id' => $id)));
        }

        return $this->render('LOCKSSOMaticPluginBundle:LomPluginsData:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a LomPluginsData entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LOCKSSOMaticPluginBundle:LomPluginsData')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find LomPluginsData entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('lompluginsdata'));
    }

    /**
     * Creates a form to delete a LomPluginsData entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lompluginsdata_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
