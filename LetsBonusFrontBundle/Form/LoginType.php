<?php

namespace iFlair\LetsBonusFrontBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'email',
                'email',
                [
                    'attr' => [
                        'class' => 'login-form form-control',
                        'required' => true,
                    ],
                ]
            )
            ->add(
                'password',
                'password',
                [
                    'attr' => [
                        'class' => 'login-form form-control',
                        'required' => true,
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'iFlair\LetsBonusAdminBundle\Entity\FrontUser',
            ]
        );
    }

    public function getName()
    {
        return '';
    }
}
