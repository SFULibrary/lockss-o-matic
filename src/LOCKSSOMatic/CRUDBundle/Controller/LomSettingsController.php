<?php

namespace LOCKSSOMatic\CRUDBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use LOCKSSOMatic\CRUDBundle\Entity\LomSettings;
use LOCKSSOMatic\CRUDBundle\Form\LomSettingsType;

/**
 * LomSettings controller.
 *
 * @Route("/lomsettings")
 */
class LomSettingsController extends Controller
{

    /**
     * Lists all LomSettings entities.
     *
     * @Route("/", name="lomsettings")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('LOCKSSOMaticCRUDBundle:LomSettings')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new LomSettings entity.
     *
     * @Route("/", name="lomsettings_create")
     * @Method("POST")
     * @Template("LOCKSSOMaticCRUDBundle:LomSettings:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new LomSettings();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('lomsettings_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a LomSettings entity.
    *
    * @param LomSettings $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(LomSettings $entity)
    {
        $form = $this->createForm(new LomSettingsType(), $entity, array(
            'action' => $this->generateUrl('lomsettings_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new LomSettings entity.
     *
     * @Route("/new", name="lomsettings_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new LomSettings();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a LomSettings entity.
     *
     * @Route("/{id}", name="lomsettings_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCRUDBundle:LomSettings')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find LomSettings entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing LomSettings entity.
     *
     * @Route("/{id}/edit", name="lomsettings_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id = 1)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCRUDBundle:LomSettings')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find LomSettings entity.');
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
    * Creates a form to edit a LomSettings entity.
    *
    * @param LomSettings $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(LomSettings $entity)
    {
        $form = $this->createForm(new LomSettingsType(), $entity, array(
            'action' => $this->generateUrl('lomsettings_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing LomSettings entity.
     *
     * @Route("/{id}", name="lomsettings_update")
     * @Method("PUT")
     * @Template("LOCKSSOMaticCRUDBundle:LomSettings:edit.html.twig")
     */
    public function updateAction(Request $request, $id = 1)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCRUDBundle:LomSettings')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find LomSettings entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('lomsettings_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a LomSettings entity.
     *
     * @Route("/{id}", name="lomsettings_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LOCKSSOMaticCRUDBundle:LomSettings')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find LomSettings entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('lomsettings'));
    }

    /**
     * Creates a form to delete a LomSettings entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lomsettings_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
