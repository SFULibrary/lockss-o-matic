<?php

namespace LOCKSSOMatic\CrudBundle\Form;

use LOCKSSOMatic\CrudBundle\Entity\Pln;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class PlnPropertyType extends AbstractType
{
    private $pln;

    private $id;

    public function __construct(Pln $pln = null, $id = null)
    {
        $this->pln = $pln;
        $this->id = $id;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = array('');
        if ($this->pln !== null && $this->id !== null) {
            $data = $this->pln->getProperty($this->id);
            if (!is_array($data)) {
                $data = array($data);
            }
        }
        $builder->add('name', 'text', array(
            'data' => $this->id,
            'label' => 'Name',
            'required' => true,
        ));
        $builder->add('value', 'collection', array(
            'data' => $data,
            'label' => 'Value',
            'type' => 'text',
            'allow_add' => true,
            'allow_delete' => true,
            'delete_empty' => true,
            'prototype' => true,
            'required' => true,
            'options' => array(
            ),
        ));
    }

    public function getName()
    {
        return 'lockssomatic_crudbundle_plnproperty';
    }
}
