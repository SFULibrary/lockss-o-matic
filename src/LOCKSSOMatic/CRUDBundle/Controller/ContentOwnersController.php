<?php

namespace LOCKSSOMatic\CRUDBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use LOCKSSOMatic\CRUDBundle\Entity\ContentOwners;
use LOCKSSOMatic\CRUDBundle\Form\ContentOwnersType;

/**
 * ContentOwners controller.
 *
 */
class ContentOwnersController extends Controller
{

    /**
     * Lists all ContentOwners entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('LOCKSSOMaticCRUDBundle:ContentOwners')->findAll();

        return $this->render('LOCKSSOMaticCRUDBundle:ContentOwners:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new ContentOwners entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new ContentOwners();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('contentowners_show', array('id' => $entity->getId())));
        }

        return $this->render('LOCKSSOMaticCRUDBundle:ContentOwners:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a ContentOwners entity.
     *
     * @param ContentOwners $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ContentOwners $entity)
    {
        $form = $this->createForm(new ContentOwnersType(), $entity, array(
            'action' => $this->generateUrl('contentowners_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new ContentOwners entity.
     *
     */
    public function newAction()
    {
        $entity = new ContentOwners();
        $form   = $this->createCreateForm($entity);

        return $this->render('LOCKSSOMaticCRUDBundle:ContentOwners:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ContentOwners entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCRUDBundle:ContentOwners')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ContentOwners entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('LOCKSSOMaticCRUDBundle:ContentOwners:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ContentOwners entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCRUDBundle:ContentOwners')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ContentOwners entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('LOCKSSOMaticCRUDBundle:ContentOwners:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a ContentOwners entity.
    *
    * @param ContentOwners $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ContentOwners $entity)
    {
        $form = $this->createForm(new ContentOwnersType(), $entity, array(
            'action' => $this->generateUrl('contentowners_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing ContentOwners entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCRUDBundle:ContentOwners')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ContentOwners entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('contentowners_edit', array('id' => $id)));
        }

        return $this->render('LOCKSSOMaticCRUDBundle:ContentOwners:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a ContentOwners entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LOCKSSOMaticCRUDBundle:ContentOwners')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ContentOwners entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('contentowners'));
    }

    /**
     * Creates a form to delete a ContentOwners entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('contentowners_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
