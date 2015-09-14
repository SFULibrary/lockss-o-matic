<?php

namespace LOCKSSOMatic\PluginBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LomPluginsDataType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('domain')
            ->add('objectId')
            ->add('key')
            ->add('value')
            ->add('lomPlugin')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LOCKSSOMatic\PluginBundle\Entity\LomPluginsData'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'lockssomatic_pluginbundle_lompluginsdata';
    }
}
