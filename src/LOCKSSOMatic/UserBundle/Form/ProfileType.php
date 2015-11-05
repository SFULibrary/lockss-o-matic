<?php

namespace LOCKSSOMatic\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class ProfileType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email');
        $builder->add('fullname');
        $builder->add('institution');
        $builder->add('current_password', 'password', array(
            'label' => 'Current password',
            'mapped' => false,
            'constraints' => new UserPassword(),
        ));
        $builder->add('submit', 'submit');
    }
    
    public function getName()
    {
        return 'lom_user_profile';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => "LOCKSSOMatic\UserBundle\Entity\User",
        ));
    }
}
