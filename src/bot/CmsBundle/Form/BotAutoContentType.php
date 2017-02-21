<?php

namespace bot\CmsBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BotAutoContentType extends AbstractType
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
                ->add('sendDate', 'datetime', array(
                    'years' => range(2016, 2050),
                    'label' => 'Автоматаар мэдээ илгээх огноо',
                    'required' => true,
                ));
        } else {

            $builder
                ->add('sendDate', 'datetime', array(
                    'years' => range(2016, 2050),
                    'data' => new \DateTime('now'),
                    'label' => 'Автоматаар мэдээ илгээх огноо',
                    'required' => true,
                ));

        }

        $builder
//
//            ->add('botContentText', 'entity', array(
//                'class' => 'botCmsBundle:BotContent',
//                'label' => 'Текст',
//                'property' => 'title',
//                'required' => false,
//                'query_builder' => function (EntityRepository $er) {
//                    return $er->createQueryBuilder('pp')
//                        ->where('pp.type = 2');
//                },
//                'attr' => array(
//                    "class" => "form-control",
//                )
//            ))

            ->add('botContent', 'entity', array(
                'class' => 'botCmsBundle:BotContent',
                'label' => 'Мэдээ',
                'property' => 'title',
                'required' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('pp')
                        ->where('pp.type = 1');
                },
                'attr' => array(
                    "class" => "form-control",
                )
            ));


    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'bot\CmsBundle\Entity\BotAutoContent'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bot_cmsbundle_block_auto';
    }
}
