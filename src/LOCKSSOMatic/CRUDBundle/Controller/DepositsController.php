<?php

namespace LOCKSSOMatic\CRUDBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use LOCKSSOMatic\CRUDBundle\Entity\Deposits;
use LOCKSSOMatic\CRUDBundle\Form\DepositsType;

/**
 * Deposits controller.
 *
 */
class DepositsController extends Controller
{

    /**
     * Lists all Deposits entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('LOCKSSOMaticCRUDBundle:Deposits')->findAll();

        return $this->render('LOCKSSOMaticCRUDBundle:Deposits:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Deposits entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Deposits();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('deposits_show', array('id' => $entity->getId())));
        }

        return $this->render('LOCKSSOMaticCRUDBundle:Deposits:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Deposits entity.
     *
     * @param Deposits $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Deposits $entity)
    {
        $form = $this->createForm(new DepositsType(), $entity, array(
            'action' => $this->generateUrl('deposits_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Deposits entity.
     *
     */
    public function newAction()
    {
        $entity = new Deposits();
        $form   = $this->createCreateForm($entity);

        return $this->render('LOCKSSOMaticCRUDBundle:Deposits:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Deposits entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCRUDBundle:Deposits')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Deposits entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('LOCKSSOMaticCRUDBundle:Deposits:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Deposits entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCRUDBundle:Deposits')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Deposits entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('LOCKSSOMaticCRUDBundle:Deposits:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Deposits entity.
    *
    * @param Deposits $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Deposits $entity)
    {
        $form = $this->createForm(new DepositsType(), $entity, array(
            'action' => $this->generateUrl('deposits_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Deposits entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCRUDBundle:Deposits')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Deposits entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('deposits_edit', array('id' => $id)));
        }

        return $this->render('LOCKSSOMaticCRUDBundle:Deposits:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Deposits entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LOCKSSOMaticCRUDBundle:Deposits')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Deposits entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('deposits'));
    }

    /**
     * Creates a form to delete a Deposits entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('deposits_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
