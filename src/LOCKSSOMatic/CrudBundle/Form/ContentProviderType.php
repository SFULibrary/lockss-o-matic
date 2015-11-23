<?php

namespace LOCKSSOMatic\CrudBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContentProviderType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('uuid', 'text',
                array(
                'required' => false,
                'attr'     => array(
                    'help' => 'Leave UUID blank to have one generated.'
            )))
            ->add('permissionurl', 'url',
                array(
                'label' => 'Permission URL',
                'attr'  => array(
                    'help' => 'URL for the LOCKSS permission statement.'
                )
            ))
            ->add('name')
            ->add('maxFileSize', 'integer',
                array(
                'attr' => array(
                    'help' => 'Maximum file size allowed in an AU, in kb (1,000 bytes)'
                )
            ))
            ->add('maxAuSize', 'integer',
                array(
                'attr' => array(
                    'help' => 'Maximum AU size, in kb (1,000 bytes)'
                )
            ))
            ->add('contentOwner')
            ->add('plugin')
            ->add('pln')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LOCKSSOMatic\CrudBundle\Entity\ContentProvider'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'lockssomatic_crudbundle_contentprovider';
    }

}
