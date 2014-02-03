<?php

namespace LOCKSSOMatic\CRUDBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PluginPropertiesType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pluginsId')
            ->add('ausId')
            ->add('parentId')
            ->add('propertyKey')
            ->add('propertyValue')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LOCKSSOMatic\CRUDBundle\Entity\PluginProperties'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'lockssomatic_crudbundle_pluginproperties';
    }
}
