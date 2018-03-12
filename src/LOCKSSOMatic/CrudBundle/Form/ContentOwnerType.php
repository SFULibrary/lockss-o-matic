<?php

namespace LOCKSSOMatic\CrudBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Content owner form.
 */
class ContentOwnerType extends AbstractType
{
    /**
     * Build a form in-place.
     *
     * @param FormBuilderInterface $builder
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('name')->add('emailAddress');
    }

    /**
     * {@inheritDocs}
      *
      * @param OptionsResolverInterface $resolver
      */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'LOCKSSOMatic\CrudBundle\Entity\ContentOwner',
        ));
    }

    /**
     * {@inheritDocs}
     */
    public function getName() {
        return 'lockssomatic_crudbundle_contentowner';
    }
}
