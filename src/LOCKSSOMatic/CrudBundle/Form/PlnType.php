<?php

namespace LOCKSSOMatic\CrudBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Build a form for editing one PLN.
 */
class PlnType extends AbstractType
{
    /**
     * Build a PLN edit form. Adds fields to $builder in-place.
     *
     * @param FormBuilderInterface $builder
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('name');
        $builder->add('description');
        $builder->add('username', 'text');
        $builder->add('password', 'password');
    }

    /**
     * {@inheritDocs}
      *
      * @param OptionsResolverInterface $resolver
      */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'LOCKSSOMatic\CrudBundle\Entity\Pln',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return 'lockssomatic_crudbundle_pln';
    }
}
