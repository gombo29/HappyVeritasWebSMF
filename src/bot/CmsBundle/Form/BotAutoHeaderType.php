<?php

namespace bot\CmsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BotAutoHeaderType extends AbstractType
{
    public $isUpdate;

    public function __construct($isUpdate)
    {
        $this->isUpdate = $isUpdate;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->isUpdate == 1) {
            $builder
                ->add('startDate', 'datetime', array(
                    'years' => range(2016, 2050),
                    'label' => 'Эхлэх огноо',
                    'required' => true,
                ))

                ->add('endDate', 'datetime', array(
                    'years' => range(2016, 2050),
                    'label' => 'Дуусах огноо',
                    'required' => true,
                ))
            ;

        } else {
            $builder
                ->add('startDate', 'datetime', array(
                    'years' => range(2016, 2050),
                    'data' => new \DateTime('now'),
                    'label' => 'Эхлэх огноо',
                    'required' => true,
                ))

                ->add('endDate', 'datetime', array(
                    'years' => range(2016, 2050),
                    'data' => new \DateTime('now'),
                    'label' => 'Дуусах огноо',
                    'required' => true,
                ))
                ;

        }

        $builder
            ->add('content', 'textarea', array(
                    'label' => 'Мэдээний агуулга',
                    'required' => true
                )
            );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'bot\CmsBundle\Entity\BotAutoHeader'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bot_cmsbundle_block_auto_header';
    }
}
