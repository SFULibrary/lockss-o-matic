<?php

namespace LOCKSSOMatic\CrudBundle\Form;

use LOCKSSOMatic\CrudBundle\Entity\Pln;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PlnType extends AbstractType
{
	/**
	 * @var Pln
	 */
	private $pln;
	
	public function __construct(Pln $pln) {
		$this->pln = $pln;
	}
	
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name');
		$builder->add('description');
		
		foreach($this->pln->getPropertyKeys() as $key) {
			$name = str_replace('.', ':', $key);
			$builder->add($name, 'collection', array(
				'type' => 'text',
				'allow_add' => true,
				'allow_delete' => true,
				'delete_empty' => true,
				'required' => false,
				'mapped' => false,
				'label' => $key,
				'data' => $this->pln->getProperty($key, true),
			));
		}
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LOCKSSOMatic\CrudBundle\Entity\Pln'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'lockssomatic_crudbundle_pln';
    }
}
