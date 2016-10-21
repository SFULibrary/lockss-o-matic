<?php

/*
 * The MIT License
 *
 * Copyright 2014-2016. Michael Joyce <ubermichael@gmail.com>.
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

namespace LOCKSSOMatic\CrudBundle\Form;

use LOCKSSOMatic\CrudBundle\Entity\Pln;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * PLN Property form. A PLN property can be multiple-valued.
 */
class PlnPropertyType extends AbstractType
{
    /**
     * The PLN for the property.
     * 
     * @var PLN
     */
    private $pln;

    /**
     * The ID of the property type.
     * 
     * @var string
     */
    private $id;

    public function __construct(Pln $pln = null, $id = null)
    {
        $this->pln = $pln;
        $this->id = $id;
    }

    /**
     * Build a form for editing one property in a PLN.
     * 
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

    /**
     * {@inheritDocs}
     */
    public function getName()
    {
        return 'lockssomatic_crudbundle_plnproperty';
    }
}
