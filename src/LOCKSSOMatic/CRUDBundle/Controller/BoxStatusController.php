<?php

namespace LOCKSSOMatic\CRUDBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use LOCKSSOMatic\CRUDBundle\Entity\BoxStatus;
use LOCKSSOMatic\CRUDBundle\Form\BoxStatusType;

/**
 * BoxStatus controller.
 *
 */
class BoxStatusController extends Controller
{

    /**
     * Lists all BoxStatus entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('LOCKSSOMaticCRUDBundle:BoxStatus')->findAll();

        return $this->render('LOCKSSOMaticCRUDBundle:BoxStatus:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new BoxStatus entity.
     *
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

        return $this->render('LOCKSSOMaticCRUDBundle:BoxStatus:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
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
     */
    public function newAction()
    {
        $entity = new BoxStatus();
        $form   = $this->createCreateForm($entity);

        return $this->render('LOCKSSOMaticCRUDBundle:BoxStatus:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a BoxStatus entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCRUDBundle:BoxStatus')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BoxStatus entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('LOCKSSOMaticCRUDBundle:BoxStatus:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing BoxStatus entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCRUDBundle:BoxStatus')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BoxStatus entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('LOCKSSOMaticCRUDBundle:BoxStatus:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
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
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCRUDBundle:BoxStatus')->find($id);

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

        return $this->render('LOCKSSOMaticCRUDBundle:BoxStatus:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a BoxStatus entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LOCKSSOMaticCRUDBundle:BoxStatus')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find BoxStatus entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

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
