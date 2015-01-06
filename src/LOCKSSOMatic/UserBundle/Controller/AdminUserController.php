<?php

/* 
 * The MIT License
 *
 * Copyright 2014. Michael Joyce <ubermichael@gmail.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace LOCKSSOMatic\UserBundle\Controller;

use LOCKSSOMatic\UserBundle\Entity\User;
use LOCKSSOMatic\UserBundle\Form\Type\AdminUserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * User controller - Admin access for CRUD operations on users.
 */
class AdminUserController extends Controller
{

    /**
     * Lists all User entities.
     *
     * @return Response the response
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('LOCKSSOMaticUserBundle:User')->findAll();

        return $this->render('LOCKSSOMaticUserBundle:AdminUser:index.html.twig', array(
                'entities' => $entities,
        ));
    }

    /**
     * Creates a new User entity. The password is set to a random value, and
     * can be reset by the user.
     *
     * @return Response the response
     */
    public function createAction(Request $request)
    {
        $entity = new User();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            // generate a random password - the user should have to reset it anyway
            // to verify the email address.
            $entity->setPlainPassword(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
            $this->get('session')->getFlashBag()->add(
                'notice',
                'The user account has been created. The user must now reset the password'
                . ' by visiting ' . $this->generateUrl('fos_user_resetting_request', array(), true)
            );
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('admin_user_show', array('id' => $entity->getId())));
        }

        return $this->render('LOCKSSOMaticUserBundle:AdminUser:new.html.twig', array(
                'entity' => $entity,
                'form' => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a User entity.
     *
     * @param User $entity The entity
     *
     * @return Form The form
     */
    private function createCreateForm(User $entity)
    {
        $form = $this->createForm(new AdminUserType(), $entity, array(
            'action' => $this->generateUrl('admin_user_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new User entity.
     *
     * @return Response the response
     */
    public function newAction()
    {
        $entity = new User();
        $form = $this->createCreateForm($entity);

        return $this->render('LOCKSSOMaticUserBundle:AdminUser:new.html.twig', array(
                'entity' => $entity,
                'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a User entity.
     *
     * @return Response the response
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('LOCKSSOMaticUserBundle:AdminUser:show.html.twig', array(
                'entity' => $entity,
                'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @return Response the response
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('LOCKSSOMaticUserBundle:AdminUser:edit.html.twig', array(
                'entity' => $entity,
                'edit_form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a User entity.
     *
     * @param User $entity The entity
     *
     * @return Form The form
     */
    private function createEditForm(User $entity)
    {
        $form = $this->createForm(new AdminUserType(), $entity, array(
            'action' => $this->generateUrl('admin_user_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing User entity.
     *
     * @return Response the response
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            return $this->redirect($this->generateUrl('admin_user_edit', array('id' => $id)));
        }

        return $this->render('LOCKSSOMaticUserBundle:AdminUser:edit.html.twig', array(
                'entity' => $entity,
                'edit_form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a User entity.
     *
     * @return Response the response
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LOCKSSOMaticUserBundle:User')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find User entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_user'));
    }

    /**
     * Creates a form to delete a User entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
                ->setAction($this->generateUrl('admin_user_delete', array('id' => $id)))
                ->setMethod('DELETE')
                ->add('submit', 'submit', array('label' => 'Delete'))
                ->getForm()
        ;
    }
}
