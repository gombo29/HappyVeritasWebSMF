<?php

namespace bot\CmsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SocialContentType extends AbstractType
{
    private $isUpdate;

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
        $builder
            ->add('tab', 'entity', array(
                'label' => 'Таб категори',
                'class' => 'botCmsBundle:SocialTabCategory',
                'property' => 'name',
                'required' => false,
                'attr' => array(
                    "class" => "form-control",
                )
            ))
            ->add('title', 'text', array(
                    'label' => 'Гарчиг'
                )
            )
            ->add('body', 'textarea', array(
                    'label' => 'Мэдээ оруулах'
                )
            )
            ->add('link', 'text', array(
                    'label' => 'Мэдээний холбоос',
                    'required' => true
                )
            )
            ->add('video', 'text', array(
                    'label' => 'Видео холбоос /Youtube видеоны v-н арын кодыг copy хийж тавина/',
                    'required' => false
                )
            );

        if ($this->isUpdate == true) {
            $builder
                ->add('isEmbed', 'choice', array(

                        'label' => 'Embed хийх эсэх',
                        'choices' => array(
                            '0' => 'Тийм',
                            '1' => 'Үгүй',
                        ),
                        'expanded' => true,
                        'required' => true,
                    )
                )
                ->add('isUpload', 'choice', array(

                        'label' => 'Зураг комьпютер-с оруулах эсэх',
                        'choices' => array(
                            '0' => 'Тийм',
                            '1' => 'Үгүй',
                        ),
                        'expanded' => true,
                        'required' => true,
                    )
                )
                ->add('isTarget', 'choice', array(

                        'label' => 'Таб дээрээс шууд үсрэх эсэх',
                        'choices' => array(
                            '1' => 'Тийм',
                            '0' => 'Үгүй',
                        ),
                        'expanded' => true,
                        'required' => true,
                    )
                )
                ->add('publishDate', 'datetime', array(
                    'years' => range(2016, 2050),
                    'label' => 'Мэдээ нийтлэх огноо',
                    'required' => true,
                ))
                ->add('endDate', 'datetime', array(
                    'years' => range(2016, 2050),
                    'label' => 'Мэдээ хаах огноо',
                    'required' => true,
                ));
        } else {
            $builder
                ->add('isEmbed', 'choice', array(

                        'label' => 'Embed хийх эсэх',
                        'choices' => array(
                            '0' => 'Тийм',
                            '1' => 'Үгүй',
                        ),
                        'data' => 1,
                        'expanded' => true,
                        'required' => true,
                    )
                )
                ->add('isUpload', 'choice', array(

                        'label' => 'Зураг комьпютер-с оруулах эсэх',
                        'choices' => array(
                            '0' => 'Тийм',
                            '1' => 'Үгүй',
                        ),
                        'data' => 1,
                        'expanded' => true,
                        'required' => true,
                    )
                )
                ->add('isTarget', 'choice', array(

                        'label' => 'Таб дээрээс шууд үсрэх эсэх',
                        'choices' => array(
                            '1' => 'Тийм',
                            '0' => 'Үгүй',
                        ),
                        'data' => 0,
                        'expanded' => true,
                        'required' => true,
                    )
                )
                ->add('publishDate', 'datetime', array(
                    'years' => range(2016, 2050),
                    'label' => 'Мэдээ нийтлэх огноо',
                    'required' => true,
                ))
                ->add('endDate', 'datetime', array(
                    'years' => range(2016, 2050),
                    'data' => new \DateTime('now'),
                    'label' => 'Мэдээ хаах огноо',
                ));
        }
        $builder
            ->add('embed', 'textarea', array(
                    'label' => 'Embed код хуулах',
                    'required' => false
                )
            )
            ->add('img', 'text', array(
                    'label' => 'Зурагны холбоос хуулна уу!',
                    'required' => false
                )
            )
            ->add('imagefile', 'file', array(
                'label' => 'Зураг оруулах',
                'required' => false,
                'attr' => array(
                    'class' => 'btn btn-success fileinput-button',
                )));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'bot\CmsBundle\Entity\SocialContent'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bot_cmsbundle_social_content';
    }
}
