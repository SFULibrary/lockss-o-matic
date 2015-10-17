<?php

namespace LOCKSSOMatic\CrudBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PlnPropertyType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('propertyKey')
            ->add('propertyValue')
            ->add('isList')
            ->add('parent')
            ->add('pln')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LOCKSSOMatic\CrudBundle\Entity\PlnProperty'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'lockssomatic_crudbundle_plnproperty';
    }
}
