<?php

namespace bot\CmsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BotContentSearchType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                    'label' => 'Гарчиг',
                    'required' => false
                )
            )
            ->add('sdate', 'datetime', array(
                'label' => 'Эхлэх огноо',
                'required' => false,
            ))
            ->add('edate', 'datetime', array(
                'label' => 'Дуусах огноо',
                'required' => false,
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'bot\CmsBundle\Entity\BotContent'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bot_cmsbundle_content_search';
    }
}
