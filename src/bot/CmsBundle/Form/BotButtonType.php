<?php

namespace bot\CmsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BotButtonType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
            $builder
                ->add('name', 'text', array(
                        'label' => 'Нэр /Дээд тал нь 80 тэмдэгт байна/',
                        'required' => true
                    )
                )

//                ->add('nameEn', 'text', array(
//                        'label' => 'Нэр[Англи] /Дээд тал нь 80 тэмдэгт байна/',
//                        'required' => true
//                    )
//                )

                ->add('botBlock', 'entity', array(
                    'label' => 'Категори сонгох',
                    'class' => 'botCmsBundle:BotBlock',
                    'property' => 'name',
                    'required' => true,
                    'attr' => array(
                        "class" => "form-control",
                    )
                ))
            ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'bot\CmsBundle\Entity\BotButton'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bot_cmsbundle_button';
    }
}
