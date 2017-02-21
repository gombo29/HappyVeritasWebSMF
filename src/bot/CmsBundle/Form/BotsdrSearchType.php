<?php

namespace bot\CmsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BotsdrSearchType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder
//            ->add('botBlock', 'entity', array(
//                'label' => 'Категори',
//                'class' => 'botCmsBundle:BotBlock',
//                'property' => 'name',
//                'required' => false,
//                'attr' => array(
//                    "class" => "form-control",
//                )
//            ))

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
            'data_class' => 'bot\CmsBundle\Entity\BotSenderNews'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bot_cmsbundle_sender_news';
    }
}
