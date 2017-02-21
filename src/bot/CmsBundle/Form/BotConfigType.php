<?php

namespace bot\CmsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BotConfigType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('contentTotal', 'number', array(
//                    'label' => 'Харагдах мэдээний тоо'
//                )
//            )
            ->add('diffTotal', 'number', array(
                    'label' => 'Автоматаар мэдээ илгээх хэрэглэгчийн хамрах хүрээ /Хоногоор/'
                )
            )

//            ->add('cronDate', 'text', array(
//                    'label' => 'Автомат мэдээний default мэдээлэл /255 тэмдэгт байна/'
//                )
//            )

            ->add('endMsgDate', 'number', array(
                    'label' => 'Төгсгөлийн мэдээлэл илгээх хугацаа /Минутаар/ Хамгийн багадаа 2 минут байна!'
                )
            )

        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'bot\CmsBundle\Entity\BotConfig'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bot_cmsbundle_config';
    }
}
