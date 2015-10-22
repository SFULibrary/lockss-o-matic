<?php

namespace LOCKSSOMatic\CrudBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use LOCKSSOMatic\CrudBundle\Entity\Pln;
use LOCKSSOMatic\CrudBundle\Form\PlnType;

/**
 * Pln controller.
 *
 * @Route("/pln")
 */
class PlnController extends Controller
{

    /**
     * Lists all Pln entities.
     *
     * @Route("/", name="pln")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM LOCKSSOMaticCrudBundle:Pln e';
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $entities = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            25
        );


        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Pln entity.
     *
     * @Route("/", name="pln_create")
     * @Method("POST")
     * @Template("LOCKSSOMaticCrudBundle:Pln:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Pln();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('pln_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Pln entity.
     *
     * @param Pln $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Pln $entity)
    {
        $form = $this->createForm(new PlnType(), $entity, array(
            'action' => $this->generateUrl('pln_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Pln entity.
     *
     * @Route("/new", name="pln_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Pln();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Pln entity.
     *
     * @Route("/{id}", name="pln_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pln entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Pln entity.
     *
     * @Route("/{id}/edit", name="pln_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pln entity.');
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
    * Creates a form to edit a Pln entity.
    *
    * @param Pln $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Pln $entity)
    {
        $form = $this->createForm(new PlnType(), $entity, array(
            'action' => $this->generateUrl('pln_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Pln entity.
     *
     * @Route("/{id}", name="pln_update")
     * @Method("PUT")
     * @Template("LOCKSSOMaticCrudBundle:Pln:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pln entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('pln_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Pln entity.
     *
     * @Route("/{id}/delete", name="pln_delete")
     */
    public function deleteAction(Request $request, $id)
    {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Pln entity.');
            }

            $em->remove($entity);
            $em->flush();

        return $this->redirect($this->generateUrl('pln'));
    }

    /**
     * Creates a form to delete a Pln entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('pln_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    /**
     * @Route("/{id}/access", name="pln_access")
     * @Template("LOCKSSOMaticCrudBundle:Pln:access.html.twig")
     * @param type $id
     */
    public function showAccessAction($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository("LOCKSSOMaticCrudBundle:Pln")->find($id);
        $this->get('lom.access')->checkAccess("PLNADMIN", $entity);
        $users = $em->getRepository("LOCKSSOMaticUserBundle:User")->findAll();
        return array(
            'users' => $users,
            'pln' => $entity,
            'levels' => PlnAccessLevels::levels()
        );
    }

    private function createEditAccessForm(Pln $pln) {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository("LOCKSSOMaticUserBundle:User")->findAll();
        $accessManager = $this->get('lom.access');
        $defaultData = array('message' => 'Edit user access');
        $levels = PlnAccessLevels::levels();
        $options = array(
            'method' => 'POST',
            'action' => $this->generateUrl(
                'pln_access_update',
                array('id' => $pln->getId())
            )
        );
        $builder = $this->createFormBuilder($defaultData, $options);
        foreach($users as $user) {
            if($user->hasRole('ROLE_ADMIN')) {
                continue; // skip admins - they can do anything.
            }
            $builder->add('user_' . $user->getId(), 'choice', array(
                'label' => $user->getFullname(),
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
     * @Route("/{id}/access/edit", name="pln_access_edit")
     * @Method("GET")
     * @Template("LOCKSSOMaticCrudBundle:Pln:accessEdit.html.twig")
     * @param type $id
     */
    public function editAccessAction($id) {
        $em = $this->getDoctrine()->getManager();
        $pln = $em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($id);
        $this->get('lom.access')->checkAccess("PLNADMIN", $pln);
        $form = $this->createEditAccessForm($pln);
        return array(
            'access_edit_form' => $form->createView(),
            'pln' => $pln
        );
    }

    private function updateAccess(Request $request, Pln $pln) {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository("LOCKSSOMaticUserBundle:User")->findAll();
        $accessManager = $this->get('lom.access');
        $formData = $request->request->all();
        $data = $formData['form'];
        foreach($users as $user) {
            $key = 'user_' . $user->getId();
            if( !array_key_exists($key, $data)) {
                continue;
            }
            $accessManager->setAccess($data[$key], $pln, $user);
        }
    }

    /**
     * @Route("/{id}/access/edit", name="pln_access_update")
     * @Method("POST")
     * @param type $id
     */
    public function updateAccessAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $pln = $em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($id);
        $this->get('lom.access')->checkAccess("PLNADMIN", $pln);
        $form = $this->createEditAccessForm($pln);
        $form->handleRequest($request);
        if($form->isValid()) {
            $this->updateAccess($request, $pln);
            $this->addFlash('success', "The form was saved.");
            return $this->redirect($this->generateUrl('pln_access', array('id' => $id)));
        } else {
            $this->addFlash('error', "The form was not saved.");
            return $this->redirect($this->generateUrl('pln_access_edit', array('id' => $id)));
        }
    }
}