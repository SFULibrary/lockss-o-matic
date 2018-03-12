<?php

namespace LOCKSSOMatic\CrudBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Build a form for editing one PLN.
 */
class PlnType extends AbstractType {

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
        $builder->add('enableContentUi', 'choice', array(
            'expanded' => true,
            'multiple' => false,
            'label' => 'Content UI',
            'choices' => array(
                '1' => 'Enabled',
                '0' => 'Disabled',
            ),
            'attr' => array(
                'help' => 'This enables the Content UI port for all the boxes in the PLN.'
                . ' If you enable this feature, you should also set the org.lockss.proxy.access.ip.include property on the PLN.'
            ),
        ));
        $builder->add('contentPort', null, array(
            'attr' => array(
                'help' => 'This sets the Content UI port for all the boxes in the PLN. Defaults to 8080.'
            ),
        ));
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
