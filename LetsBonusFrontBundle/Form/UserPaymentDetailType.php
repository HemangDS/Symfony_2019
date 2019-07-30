<?php

namespace iFlair\LetsBonusFrontBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserPaymentDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'ownername',
                'text',
                [
                    'attr' => [
                        'class' => 'form-control',
                        'required' => false,
                    ],
                ]
            )
            ->add(
                'accountnumber',
                'text',
                [
                    'attr' => [
                        'class' => 'form-control',
                        'required' => false,
                    ],
                ]
            )
            ->add(
                'swiftcodebic',
                'text',
                [
                    'attr' => [
                        'class' => 'form-control',
                        'required' => false,
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'iFlair\LetsBonusAdminBundle\Entity\UserPaymentDetail',
            ]
        );
    }

    public function getName()
    {
        return '';
    }
}
