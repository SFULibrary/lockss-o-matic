<?php

namespace LOCKSSOMatic\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AdminUserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'))
            ->add('fullname')
            ->add('institution')
            ->add('enabled', 'checkbox', array(
                'label' => 'Account Enabled'
            ))
            ->add('roles', 'choice', array(
                'label' => 'Roles',
                'choices' => array(
                    'ROLE_ADMIN' => 'LOM Admin',
                    'ROLE_LOMADMIN' => 'PLN Admin',
                ),
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LOCKSSOMatic\UserBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'lockssomatic_userbundle_user';
    }
}
