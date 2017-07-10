<?php

namespace LOCKSSOMatic\CrudBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Box edit form.
 */
class BoxType extends AbstractType
{
    /**
     * Build a box edit form in-place.
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('hostname', 'text', array(
                'required' => false,
                'attr' => array(
                    'class' => 'hostname',
        ), ))->add('protocol', 'text', array(
                'attr' => array(
                    'help' => 'LOCKSS internal communication protocol. Almost certainly "TCP".'
                ),
        ))->add('ipAddress', 'text', array(
                'required' => false,
                'attr' => array(
                    'class' => 'ipAddress ',
                    'help' => 'LOCKSSOMatic will look up the IP address if it is blank.',
                ),
        ))->add('port', 'text', array(
                'label' => 'LOCKSS Port',
                'attr' => array(
                    'help' => 'This is the port number that LOCKSS uses for internal communication, usually 9729.'
                )
        ))->add('webServicePort', 'text', array(
                'label' => 'LOCKSS UI Port',
                'attr' => array(
                    'help' => 'This is the web front end and SOAP Port, usually 8081.'
                )
        ))->add('pln');
    }

    /**
     * {@inheritDocs}
      *
      * @param OptionsResolverInterface $resolver
      */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'LOCKSSOMatic\CrudBundle\Entity\Box',
        ));
    }

    /**
     * {@inheritDocs}
     */
    public function getName() {
        return 'lockssomatic_crudbundle_box';
    }
}
