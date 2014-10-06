<?php

namespace LOCKSSOMatic\CRUDBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use LOCKSSOMatic\CRUDBundle\Entity\AuStatus;
use LOCKSSOMatic\CRUDBundle\Form\AuStatusType;

/**
 * AuStatus controller.
 *
 */
class AuStatusController extends Controller
{

    /**
     * Lists all AuStatus entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('LOCKSSOMaticCRUDBundle:AuStatus')->findAll();

        return $this->render('LOCKSSOMaticCRUDBundle:AuStatus:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new AuStatus entity.
     *
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

        return $this->render('LOCKSSOMaticCRUDBundle:AuStatus:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
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
     */
    public function newAction()
    {
        $entity = new AuStatus();
        $form   = $this->createCreateForm($entity);

        return $this->render('LOCKSSOMaticCRUDBundle:AuStatus:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a AuStatus entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCRUDBundle:AuStatus')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AuStatus entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('LOCKSSOMaticCRUDBundle:AuStatus:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing AuStatus entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCRUDBundle:AuStatus')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AuStatus entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('LOCKSSOMaticCRUDBundle:AuStatus:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
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
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCRUDBundle:AuStatus')->find($id);

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

        return $this->render('LOCKSSOMaticCRUDBundle:AuStatus:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a AuStatus entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LOCKSSOMaticCRUDBundle:AuStatus')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AuStatus entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

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
