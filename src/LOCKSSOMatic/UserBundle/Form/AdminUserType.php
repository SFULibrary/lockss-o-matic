<?php

namespace LOCKSSOMatic\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Form to edit a user.
 */
class AdminUserType extends AbstractType
{
    /**
     * Build the form.
     *
     * @param FormBuilderInterface $builder
     */
    public function buildForm(FormBuilderInterface $builder) {
        $builder->add('email', 'email', array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'))->add('fullname')->add('institution')->add('enabled', 'checkbox', array(
                'label' => 'Account Enabled',
        ))->add('roles', 'choice', array(
                'label' => 'Roles',
                'choices' => array(
                    'ROLE_ADMIN' => 'Full Admin',
                ),
                'multiple' => true,
                'expanded' => true,
                'required' => false,
        ));
    }

    /**
     * Set the default options.
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'LOCKSSOMatic\UserBundle\Entity\User',
        ));
    }

    /**
     * Get the form name.
     *
     * @return string
     */
    public function getName() {
        return 'lockssomatic_userbundle_user';
    }
}
