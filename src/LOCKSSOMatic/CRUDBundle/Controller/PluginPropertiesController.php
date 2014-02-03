<?php

namespace LOCKSSOMatic\CRUDBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use LOCKSSOMatic\CRUDBundle\Entity\PluginProperties;
use LOCKSSOMatic\CRUDBundle\Form\PluginPropertiesType;

/**
 * PluginProperties controller.
 *
 */
class PluginPropertiesController extends Controller
{

    /**
     * Lists all PluginProperties entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('LOCKSSOMaticCRUDBundle:PluginProperties')->findAll();

        return $this->render('LOCKSSOMaticCRUDBundle:PluginProperties:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new PluginProperties entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new PluginProperties();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('pluginproperties_show', array('id' => $entity->getId())));
        }

        return $this->render('LOCKSSOMaticCRUDBundle:PluginProperties:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
    * Creates a form to create a PluginProperties entity.
    *
    * @param PluginProperties $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(PluginProperties $entity)
    {
        $form = $this->createForm(new PluginPropertiesType(), $entity, array(
            'action' => $this->generateUrl('pluginproperties_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new PluginProperties entity.
     *
     */
    public function newAction()
    {
        $entity = new PluginProperties();
        $form   = $this->createCreateForm($entity);

        return $this->render('LOCKSSOMaticCRUDBundle:PluginProperties:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a PluginProperties entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCRUDBundle:PluginProperties')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PluginProperties entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('LOCKSSOMaticCRUDBundle:PluginProperties:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing PluginProperties entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCRUDBundle:PluginProperties')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PluginProperties entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('LOCKSSOMaticCRUDBundle:PluginProperties:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a PluginProperties entity.
    *
    * @param PluginProperties $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(PluginProperties $entity)
    {
        $form = $this->createForm(new PluginPropertiesType(), $entity, array(
            'action' => $this->generateUrl('pluginproperties_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing PluginProperties entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCRUDBundle:PluginProperties')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PluginProperties entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('pluginproperties_edit', array('id' => $id)));
        }

        return $this->render('LOCKSSOMaticCRUDBundle:PluginProperties:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a PluginProperties entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LOCKSSOMaticCRUDBundle:PluginProperties')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find PluginProperties entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('pluginproperties'));
    }

    /**
     * Creates a form to delete a PluginProperties entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('pluginproperties_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
