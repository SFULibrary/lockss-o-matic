<?php

namespace LOCKSSOMatic\CrudBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BoxType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('hostname', 'text', array('attr' => array('class' => 'hostname')))
            ->add('protocol')
            ->add('ipAddress', 'text', array(
                'required' => false,
                'attr' => array(
                    'class' => 'ipAddress ',
                    'help' => 'LOCKSSOMatic will look up the IP address if it is blank.',
                )
            ))
            ->add('port')
            ->add('pln')
            ->add('username', 'text', array(
                'required' => false,
            ))
            ->add('password', 'password', array(
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
            'data_class' => 'LOCKSSOMatic\CrudBundle\Entity\Box'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'lockssomatic_crudbundle_box';
    }
}
