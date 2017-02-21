<?php

namespace bot\CmsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AdminlogSearchType extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add ( 'username', 'text', array (
				'label' => 'Хэн',
				'required' => false 
		) )->add ( 'fromognoo', 'datetime', array (
				'label' => 'Хэзээ',
				'required' => false,
				'years' => range ( 2013, date ( 'Y' ) ) 
		) )->add ( 'toognoo', 'datetime', array (
				'label' => ' ',
				'required' => false,
				'years' => range ( 2013, date ( 'Y' ) ) 
		) )->add ( 'log', 'text', array (
				'label' => 'Юу',
				'required' => false 
		) )->add ( 'ip', 'text', array (
				'label' => 'хаанаас',
				'required' => false 
		) );
	}
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults ( array (
				'data_class' => 'bot\CmsBundle\Entity\BotLog'
		) );
	}
	public function getName() {
		return 'gogo_adminbundle_adminlog';
	}
}
