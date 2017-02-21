<?php

namespace bot\CmsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BotContentType extends AbstractType
{
    public $type;
    public $isUpdate;
    public $m;
    public $bid;

    public function __construct($type, $isUpdate, $m, $bid)
    {
        $this->type = $type;
        $this->isUpdate = $isUpdate;
        $this->m = $m;
        $this->bid = $bid;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        if ($this->type == 1) { //Мэдээ

            $builder
                ->add('title', 'text', array(
                        'label' => 'Гарчиг',
                        'required' => true
                    )
                )
//                ->add('titleEn', 'text', array(
//                        'label' => 'Гарчиг[Англи]',
//                        'required' => true
//                    )
//                )
            ;
            if ($this->isUpdate == 1) {  //Засвар хийж байгаа үед
                $builder
                    ->add('botBlock', 'entity', array(
                        'label' => 'Категори',
                        'class' => 'botCmsBundle:BotBlock',
                        'property' => 'name',
                        'required' => true,
                        'attr' => array(
                            "class" => "form-control",
                        )
                    ));
            } else {
                if ($this->bid == 0) { //Оператор оруулахад
                    $builder
                        ->add('botBlock', 'entity', array(
                            'label' => 'Категори',
                            'class' => 'botCmsBundle:BotBlock',
                            'property' => 'name',
                            'required' => true,
                            'attr' => array(
                                "class" => "form-control",
                            )
                        ));
                }

            }
            $builder
                ->add('link', 'text', array(
                        'label' => 'Мэдээний холбоос',
                        'required' => false
                    )
                )
                ->add('desc', 'textarea', array(
                        'label' => 'Тайлбар',
                        'required' => false
                    )
                )
//                ->add('descEn', 'textarea', array(
//                        'label' => 'Тайлбар[Англи]',
//                        'required' => false
//                    )
//                )
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

            if ($this->isUpdate == 1) {  //Засвар хийж байгаа үед
                $builder
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
                    ->add('publishDate', 'datetime', array(
                        'label' => 'Мэдээ нийтлэх огноо',
                        'required' => true,
                    ))
                    ->add('endDate', 'datetime', array(
                        'label' => 'Мэдээ хаах огноо',
                        'required' => true,
                    ));
            } else {
                $builder
                    ->add('isUpload', 'choice', array(

                            'label' => 'Зураг комьпютер-с оруулах эсэх',
                            'choices' => array(
                                '0' => 'Тийм',
                                '1' => 'Үгүй',
                            ),
                            'data' => '0',
                            'expanded' => true,
                            'required' => true,
                        )
                    )
                    ->add('publishDate', 'datetime', array(
                        'years' => range(2016, 2050),
                        'data' => new \DateTime('now'),
                        'label' => 'Мэдээ нийтлэх огноо',
                        'required' => true,
                    ))
                    ->add('endDate', 'datetime', array(
                        'years' => range(2016, 2050),
                        'data' => new \DateTime('now'),
                        'label' => 'Мэдээ хаах огноо',
                        'required' => true,
                    ));
            }


        } elseif ($this->type == 2) {  //Текст

            if ($this->m == true) {
                $builder
                    ->add('title', 'text', array(
                            'label' => 'Keyword /Дээд тал нь 640 тэмдэгт байна/',
                            'required' => true
                        )
                    )
//                    ->add('titleEn', 'text', array(
//                            'label' => 'Keyword[Англи] /Дээд тал нь 640 тэмдэгт байна/',
//                            'required' => true
//                        )
//                    )
                    ->add('desc', 'text', array(
                            'label' => 'Result',
                            'required' => true
                        )
                    )
//                    ->add('descEn', 'text', array(
//                            'label' => 'Result[Англи]',
//                            'required' => true
//                        )
//                    )
                ;
            } else {
                $builder
                    ->add('title', 'text', array(
                            'label' => 'Текст /Дээд тал нь 640 тэмдэгт байна/',
                            'required' => true
                        )
                    )
//                    ->add('titleEn', 'text', array(
//                            'label' => 'Текст[Англи] /Дээд тал нь 640 тэмдэгт байна/',
//                            'required' => true
//                        )
//                    )

                ;
            }

        } elseif ($this->type == 3) {  //Зураг


            if ($this->isUpdate == 1) {
                $builder
                    ->add('isUpload', 'choice', array(

                            'label' => 'Зураг комьпютер-с оруулах эсэх',
                            'choices' => array(
                                '0' => 'Тийм',
                                '1' => 'Үгүй',
                            ),
                            'expanded' => true,
                            'required' => true,
                        )
                    );
            } else {
                $builder
                    ->add('isUpload', 'choice', array(

                            'label' => 'Зураг комьпютер-с оруулах эсэх',
                            'choices' => array(
                                '0' => 'Тийм',
                                '1' => 'Үгүй',
                            ),
                            'data' => '0',
                            'expanded' => true,
                            'required' => true,
                        )
                    );
            }
            $builder
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
        return 'bot_cmsbundle_content';
    }
}
