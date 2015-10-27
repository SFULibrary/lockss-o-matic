<?php

namespace LOCKSSOMatic\CrudBundle\Controller;

use J20\Uuid\Uuid;
use LOCKSSOMatic\CrudBundle\Entity\ContentProvider;
use LOCKSSOMatic\CrudBundle\Form\ContentProviderType;
use LOCKSSOMatic\CrudBundle\Service\DepositBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ContentProvider controller.
 *
 * @Route("/contentprovider")
 */
class ContentProviderController extends Controller
{

    /**
     * Lists all ContentProvider entities.
     *
     * @Route("/", name="contentprovider")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM LOCKSSOMaticCrudBundle:ContentProvider e';
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
     * Creates a new ContentProvider entity.
     *
     * @Route("/", name="contentprovider_create")
     * @Method("POST")
     * @Template("LOCKSSOMaticCrudBundle:ContentProvider:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new ContentProvider();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if ($entity->getUuid() === null || $entity->getUuid() === '') {
                $entity->setUuid(Uuid::v4());
            }
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl(
                'contentprovider_show',
                array('id' => $entity->getId())
            ));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a ContentProvider entity.
     *
     * @param ContentProvider $entity The entity
     *
     * @return Form The form
     */
    private function createCreateForm(ContentProvider $entity)
    {
        $form = $this->createForm(
            new ContentProviderType(),
            $entity,
            array(
            'action' => $this->generateUrl('contentprovider_create'),
            'method' => 'POST',
            )
        );

            $form->add('submit', 'submit', array('label' => 'Create'));

            return $form;
    }

    /**
     * Displays a form to create a new ContentProvider entity.
     *
     * @Route("/new", name="contentprovider_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new ContentProvider();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a ContentProvider entity.
     *
     * @Route("/{id}", name="contentprovider_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:ContentProvider')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ContentProvider entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing ContentProvider entity.
     *
     * @Route("/{id}/edit", name="contentprovider_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:ContentProvider')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ContentProvider entity.');
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
     * Creates a form to edit a ContentProvider entity.
     *
     * @param ContentProvider $entity The entity
     *
     * @return Form The form
     */
    private function createEditForm(ContentProvider $entity)
    {
        $form = $this->createForm(
            new ContentProviderType(),
            $entity,
            array(
            'action' => $this->generateUrl(
                'contentprovider_update',
                array('id' => $entity->getId())
            ),
            'method' => 'PUT',
            )
        );

            $form->add('submit', 'submit', array('label' => 'Update'));

            return $form;
    }

    /**
     * Edits an existing ContentProvider entity.
     *
     * @Route("/{id}", name="contentprovider_update")
     * @Method("PUT")
     * @Template("LOCKSSOMaticCrudBundle:ContentProvider:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:ContentProvider')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ContentProvider entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl(
                'contentprovider_edit',
                array('id' => $id)
            ));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a ContentProvider entity.
     *
     * @Route("/{id}/delete", name="contentprovider_delete")
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LOCKSSOMaticCrudBundle:ContentProvider')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ContentProvider entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('contentprovider'));
    }

    /**
     * Creates a form to delete a ContentProvider entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
                ->setAction($this->generateUrl(
                    'contentprovider_delete',
                    array('id' => $id)
                ))
                ->setMethod('DELETE')
                ->add('submit', 'submit', array('label' => 'Delete'))
                ->getForm()
        ;
    }

    /**
     * Import a CSV file.
     *
     * @param Request $request
     * @Route("/{id}/csv-sample", name="contentprovider_csv_sample")
     * @Method({"GET"})
     */
    public function csvSampleAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $provider = $em->getRepository('LOCKSSOMaticCrudBundle:ContentProvider')->find($id);
        $params = array_merge(
            array('URL', 'journalTitle', 'Size', 'Checksum Type', 'Checksum Value'),
            $provider->getPlugin()->getDefinitionalProperties()
        );
        $fh = fopen("php://temp", 'r+');
        fputcsv($fh, $params);
        rewind($fh);
        return new Response(
            stream_get_contents($fh),
            Response::HTTP_OK,
            array(
            'Content-Type' => 'text/csv',
            )
        );
    }

    private function createImportForm($id)
    {
        $formBuilder = $this->createFormBuilder();
        $formBuilder->add(
            'uuid',
            'text',
            array(
            'label'    => 'Deposit UUID',
            'required' => false,
            'attr'     => array(
                'help' => 'Leave UUID blank to have one generated.'
            )
            )
        );
            $formBuilder->add('title', 'text');
            $formBuilder->add('summary', 'textarea');
            $formBuilder->add('file', 'file', array('label' => 'CSV File'));
            $formBuilder->add('submit', 'submit', array('label' => 'Import'));
            $formBuilder->setAction($this->generateUrl(
                'contentprovider_csv_import',
                array('id' => $id)
            ));
            $formBuilder->setMethod('POST');
            return $formBuilder->getForm();
    }

    /**
     * Import a CSV file.
     *
     * @param Request $request
     * @Route("/{id}/csv", name="contentprovider_csv_import")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function csvAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $provider = $em->getRepository('LOCKSSOMaticCrudBundle:ContentProvider')->find($id);
        $requiredParams = $provider->getPlugin()->getDefinitionalProperties();

        $form = $this->createImportForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var DepositBuilder $builder */
            $depositBuilder = $this->container->get('crud.builder.deposit');
            $contentBuilder = $this->container->get('crud.builder.content');
            $auBuilder = $this->container->get('crud.builder.au');

            $deposit = $depositBuilder->fromForm($form, $provider, $em);
            $data = $form->getData();
            
            $dataFile = $data['file'];
            $fh = $dataFile->openFile();
            $headers = array_map(function ($h) {
                return strtolower($h);
            }, $fh->fgetcsv());
            $headerIdx = array_flip($headers);

            while ($row = $fh->fgetcsv()) {
                if (count($row) < 2) {
                    break;
                }
                $content = $contentBuilder->fromArray($row, $headerIdx);
                $content->setDeposit($deposit);
                $auid = $content->generateAuid();
                $au = $em->getRepository('LOCKSSOMaticCrudBundle:Au')->findOneBy(array(
                    'auid' => $auid
                ));
                if ($au === null) {
                    $au = $auBuilder->fromContent($content);
                }
                $content->setAu($au);
            }
            $em->flush();
            return $this->redirect($this->generateUrl(
                'deposit_show',
                array('id' => $deposit->getId())
            ));
        }
        return array(
            'entity'   => $provider,
            'required' => $requiredParams,
            'form'     => $form->createView(),
        );
    }
}
