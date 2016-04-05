<?php

namespace LOCKSSOMatic\CrudBundle\Controller;

use LOCKSSOMatic\CrudBundle\Entity\Pln;
use LOCKSSOMatic\CrudBundle\Form\PlnPropertyType;
use LOCKSSOMatic\SwordBundle\Exceptions\BadRequestException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * PlnProperty Controller.
 * 
 * @Route("/pln/{plnId}/property")
 */
class PlnPropertyController extends Controller
{
    /**
     * List all PLN properties.
     * 
     * @Route("/", name="plnproperty")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($plnId) {
        $em = $this->getDoctrine()->getManager();
        $pln = $em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($plnId);
        if ($pln === null) {
            throw new BadRequestException();
        }
        return array(
            'pln' => $pln
        );
    }
    
    /**
     * Create a new property.
     * 
     * @param Request $request
     * @param int $plnId
     * @Route("/", name="plnproperty_create")
     * @Method("POST")
     * @Template()
     */
    public function createAction(Request $request, $plnId) {
        $em = $this->getDoctrine()->getManager();
        $pln = $em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($plnId);
        if ($pln === null) {
            throw new BadRequestException();
        }
        $form = $this->createCreateForm($pln);
        $form->handleRequest($request);
        if($form->isValid()) {
            $data = $form->getData();
            $pln->setProperty($data['name'], $data['value']);
            $this->addFlash('success', 'The property has been added.');
            $em->flush();
            return $this->redirect($this->generateUrl('plnproperty', array(
                'plnId' => $pln->getId(),
            )));
        }
        return array(
           'pln' => $pln,
           'form' => $form->createView(),
        );
   }
    
    /**
     * Creates a form to create a new property for the Pln entity.
     * 
     * @param Pln $pln
     * @return Form the form
     */
    private function createCreateForm(Pln $pln) {
        $form = $this->createForm(
            new PlnPropertyType($pln),
            null,
            array(
                'action' => $this->generateUrl('plnproperty_create', array(
                    'plnId' => $pln->getId(),
                    'method' => 'POST'
                ))
            )
        );
        $form->add('submit', 'submit', array('label' => 'Create'));
        return $form;
    }
    
    /**
     * Creates a form to create a box entity for the Pln entity.
     * 
     * @Route("/new", name="plnproperty_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction($plnId) {
        $em = $this->getDoctrine()->getManager();
        $pln = $em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($plnId);
        if ($pln === null) {
            throw new BadRequestException();
        }
        $form = $this->createCreateForm($pln);
        return array(
            'pln' => $pln,
            'form' => $form->createView(),
        );
    }
    
    /**
     * Displays a form to edit an existing Pln Property.
     * 
     * @param int $plnId
     * @param string $id
     * @Route("/{id}/edit", name="plnproperty_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($plnId, $id) {
        $em = $this->getDoctrine()->getManager();
        $pln = $em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($plnId);
        if ($pln === null) {
            throw new BadRequestException();
        }
        $editForm = $this->createEditForm($pln, $id);
        return array(
            'entity' => $pln,
            'edit_form' => $editForm->createView(),
        );
    }
    
    /**
     * Creates a form to edit a property.
     * 
     * @param Pln $pln
     * @param string $id
     * @return Form the form
     */
    private function createEditForm(Pln $pln, $id) {
        $form = $this->createForm(
            new PlnPropertyType($pln, $id),
            null,
            array(
                'action' => $this->generateUrl('plnproperty_update', array(
                    'plnId' => $pln->getId(),
                    'id' => $id
                )),
                'method' => 'PUT',
            )
        );        
        $form->add('submit', 'submit', array('label' => 'Update'));
        return $form;            
    }
    
    /**
     * Edits a PLN property.
     * 
     * @param Request $request
     * @param int $plnId
     * @param string $id
     * @Route("/{id}", name="plnproperty_update")
     * @Method("PUT")
     * @Template("LOCKSSOMaticCrudBundle:PlnProperty:edit.html.twig")
     */
    public function updateAction(Request $request, $plnId, $id) {
        $em = $this->getDoctrine()->getManager();
        $pln = $em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($plnId);
        if ($pln === null) {
            throw new BadRequestException();
        }
        $form = $this->createEditForm($pln, $id);
        $form->handleRequest($request);
        $data = $form->getData();
        if($form->isValid()) {
            $data = $form->getData();
            if(count($data['value']) > 1) {
                $pln->setProperty($data['name'], $data['value']);
            } else {
                $pln->setProperty($data['name'], $data['value'][0]);
            }
            $this->addFlash('success', 'The property has been updated.');
            $em->flush();
            return $this->redirect($this->generateUrl('plnproperty', array(
                'plnId' => $pln->getId(),
            )));
        }
        $this->addFlash('failure', 'The form was not valid.');
        return array(
            'entity' => $pln,
            'edit_form' => $form->createView(),
        );
    }
    
    /**
     * Deletes a Pln Property
     * @Route("/{id}/delete", name="plnproperty_delete")
     */
    public function deleteAction(Request $request, $plnId, $id) {
        $em = $this->getDoctrine()->getManager();
        $pln = $em->getRepository('LOCKSSOMaticCrudBundle:Pln')->find($plnId);
        if ($pln === null) {
            throw new BadRequestException();
        }
        $pln->deleteProperty($id);
        $em->flush();
        $this->addFlash('success', 'The property has been removed.');
        return $this->redirect($this->generateUrl('plnproperty', array(
            'plnId' => $pln->getId(),
        )));
    }
    
}
