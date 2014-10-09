<?php

namespace LOCKSSOMatic\CRUDBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use LOCKSSOMatic\CRUDBundle\Entity\ExternalTitleDbs;
use LOCKSSOMatic\CRUDBundle\Form\ExternalTitleDbsType;

/**
 * ExternalTitleDbs controller.
 *
 */
class ExternalTitleDbsController extends Controller
{

    /**
     * Lists all ExternalTitleDbs entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('LOCKSSOMaticCRUDBundle:ExternalTitleDbs')->findAll();

        return $this->render('LOCKSSOMaticCRUDBundle:ExternalTitleDbs:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new ExternalTitleDbs entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new ExternalTitleDbs();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('externaltitledbs_show', array('id' => $entity->getId())));
        }

        return $this->render('LOCKSSOMaticCRUDBundle:ExternalTitleDbs:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a ExternalTitleDbs entity.
     *
     * @param ExternalTitleDbs $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ExternalTitleDbs $entity)
    {
        $form = $this->createForm(new ExternalTitleDbsType(), $entity, array(
            'action' => $this->generateUrl('externaltitledbs_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new ExternalTitleDbs entity.
     *
     */
    public function newAction()
    {
        $entity = new ExternalTitleDbs();
        $form   = $this->createCreateForm($entity);

        return $this->render('LOCKSSOMaticCRUDBundle:ExternalTitleDbs:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ExternalTitleDbs entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCRUDBundle:ExternalTitleDbs')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ExternalTitleDbs entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('LOCKSSOMaticCRUDBundle:ExternalTitleDbs:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ExternalTitleDbs entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCRUDBundle:ExternalTitleDbs')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ExternalTitleDbs entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('LOCKSSOMaticCRUDBundle:ExternalTitleDbs:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a ExternalTitleDbs entity.
    *
    * @param ExternalTitleDbs $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ExternalTitleDbs $entity)
    {
        $form = $this->createForm(new ExternalTitleDbsType(), $entity, array(
            'action' => $this->generateUrl('externaltitledbs_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing ExternalTitleDbs entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCRUDBundle:ExternalTitleDbs')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ExternalTitleDbs entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('externaltitledbs_edit', array('id' => $id)));
        }

        return $this->render('LOCKSSOMaticCRUDBundle:ExternalTitleDbs:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a ExternalTitleDbs entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LOCKSSOMaticCRUDBundle:ExternalTitleDbs')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ExternalTitleDbs entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('externaltitledbs'));
    }

    /**
     * Creates a form to delete a ExternalTitleDbs entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('externaltitledbs_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
