<?php

namespace LOCKSSOMatic\UserBundle\Controller;

use LOCKSSOMatic\CrudBundle\Entity\Pln;
use LOCKSSOMatic\UserBundle\Entity\User;
use LOCKSSOMatic\UserBundle\Form\AdminUserType;
use LOCKSSOMatic\UserBundle\Security\Acl\Permission\PlnAccessLevels;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * User controller.
 *
 * @Route("/admin/user")
 */
class AdminUserController extends Controller
{
    /**
     * Lists all User entities.
     *
     * @Route("/", name="admin_user")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('LOCKSSOMaticUserBundle:User')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new User entity.
     *
     * @Route("/", name="admin_user_create")
     * @Method("POST")
     * @Template("LOCKSSOMaticUserBundle:AdminUser:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new User();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setPlainPassword(bin2hex(random_bytes(10)));
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this->addFlash('success', 'The user account has been created with a random password. The user should start password recovery to login.');

            return $this->redirect($this->generateUrl('admin_user_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
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
     * @Route("/new", name="admin_user_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new User();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a User entity.
     *
     * @Route("/{id}", name="admin_user_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/{id}/edit", name="admin_user_edit")
     * @Method("GET")
     * @Template()
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

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
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
     * @Route("/{id}", name="admin_user_update")
     * @Method("PUT")
     * @Template("LOCKSSOMaticUserBundle:AdminUser:edit.html.twig")
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

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a User entity.
     *
     * @Route("/{id}", name="admin_user_delete")
     * @Method("DELETE")
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

    /**
     * Show the access levels for the user in the Plns.
     *
     * @Route("/{id}/access", name="admin_user_access")
     * @Template("LOCKSSOMaticUserBundle:AdminUser:access.html.twig")
     *
     * @param type $id
     */
    public function showAccessAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('LOCKSSOMaticUserBundle:User')->find($id);
        $plns = $em->getRepository('LOCKSSOMaticCrudBundle:Pln')->findAll();

        return array(
            'user' => $user,
            'plns' => $plns,
            'levels' => PlnAccessLevels::levels(),
        );
    }

    private function createEditAccessForm(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $plns = $em->getRepository('LOCKSSOMaticCrudBundle:Pln')->findAll();
        $accessManager = $this->get('lom.access');
        $defaultData = array('message' => 'Edit user access');
        $levels = PlnAccessLevels::levels();
        $options = array(
            'method' => 'POST',
            'action' => $this->generateUrl(
                'user_access_update',
                array('id' => $user->getId())
            ),
        );
        $builder = $this->createFormBuilder($defaultData, $options);
        foreach ($plns as $pln) {
            $builder->add('pln_'.$pln->getId(), 'choice', array(
                'label' => $pln->getName(),
                'choices' => $levels,
                'empty_value' => 'No access',
                'data' => $accessManager->findAccessLevel($user, $pln),
                'multiple' => false,
                'expanded' => false,
                'mapped' => false,
                'required' => false,
            ));
        }
        $builder->add('submit', 'submit', array('label' => 'Update'));

        return $builder->getForm();
    }

    /**
     * @Route("/{id}/access/edit", name="user_access_edit")
     * @Method("GET")
     * @Template("LOCKSSOMaticUserBundle:AdminUser:accessEdit.html.twig")
     *
     * @param type $id
     */
    public function editAccessAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('LOCKSSOMaticUserBundle:User')->find($id);
        $form = $this->createEditAccessForm($user);

        return array(
            'user' => $user,
            'form' => $form->createView(),
        );
    }

    private function updateAccess(Request $request, User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $plns = $em->getRepository('LOCKSSOMaticCrudBundle:Pln')->findAll();
        $accessManager = $this->get('lom.access');
        $formData = $request->request->all();
        $data = $formData['form'];
        foreach ($plns as $pln) {
            $key = 'pln_'.$pln->getId();
            if (!array_key_exists($key, $data)) {
                continue;
            }
            $accessManager->setAccess($data[$key], $pln, $user);
        }
    }

    /**
     * @Route("/{id}/access/edit", name="user_access_update")
     * @Method("POST")
     *
     * @param Request $request
     * @param type    $id
     */
    public function updateAccessAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('LOCKSSOMaticUserBundle:User')->find($id);
        $form = $this->createEditAccessForm($user);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->updateAccess($request, $user);
            $this->addFlash('success', 'The form was saved.');

            return $this->redirect($this->generateUrl('admin_user_access', array('id' => $id)));
        }
        $this->addFlash('error', 'The form was not saved.');

        return $this->redirect($this->generateUrl('user_access_edit', array('id' => $id)));
    }
}
