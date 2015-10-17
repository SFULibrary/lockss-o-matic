<?php

namespace LOCKSSOMatic\CrudBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DepositType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('uuid')
            ->add('title')
            ->add('summary')
            ->add('dateDeposited')
            ->add('contentProvider')
            ->add('user')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LOCKSSOMatic\CrudBundle\Entity\Deposit'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'lockssomatic_crudbundle_deposit';
    }
}
