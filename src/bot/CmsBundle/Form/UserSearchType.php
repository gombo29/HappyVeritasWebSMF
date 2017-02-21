<?php

namespace bot\CmsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserSearchType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', 'text', array(
                    'label' => 'first name',
                    'required' => false
                )
            )

            ->add('lastName', 'text', array(
                    'label' => 'Last name',
                    'required' => false
                )
            )

            ->add('gender', 'choice',array(

                    'label' => 'Хүйс',
                    'choices' => array(
                        'male' => 'male',
                        'female' => 'female',
                    ),
                    'expanded' => true,
                    'required' => false,
                )
            )

            ->add('sdate', 'datetime', array(
                'label' => 'Эхлэх огноо',
                'required' => false,
            ))
            ->add('edate', 'datetime', array(
                'label' => 'Дуусан огноо',
                'required' => false,
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'bot\CmsBundle\Entity\BotSender'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bot_cmsbundle_sender';
    }
}
