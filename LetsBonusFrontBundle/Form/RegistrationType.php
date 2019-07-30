<?php

namespace iFlair\LetsBonusFrontBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                'text',
                [
                    'attr' => [
                        'class' => 'form-control',
                        'required' => true,
                    ],
                ]
            )
            ->add(
                'surname',
                'text',
                [
                    'attr' => [
                        'class' => 'form-control',
                        'required' => true,
                    ],
                ]
            )
            ->add(
                'email',
                'email',
                [
                    'attr' => [
                        'class' => 'form-control',
                        'required' => true,
                    ],
                ]
            )
            ->add(
                'userGender',
                'choice',
                [
                    'attr' => [
                        'class' => 'gender',
                    ],
                    'choices' => [
                        '0' => 'Sr',
                        '1' => 'Sra',
                    ],
                    'multiple' => false,
                    'expanded' => true,
                    'required' => true,
                ]
            )
            ->add(
                'password',
                'repeated',
                [
                    'type' => 'password',
                    'attr' => [
                        'class' => 'form-control password',
                    ],
                    'options' => ['translation_domain' => 'FOSUserBundle'],
                    'first_options' => ['label' => 'form.password'],
                    'second_options' => ['label' => 'form.password_confirmation'],
                    'invalid_message' => 'fos_user.password.mismatch',
                    'required' => true,
                ]
            )
            ->add(
                'userBirthDate',
                'date',
                [
                    'label' => 'Expiration date',
                    'widget' => 'choice',
                    'empty_value' => ['year' => 'YYYY', 'month' => 'MM', 'day' => 'DD'],
                    'format' => 'ddMMyyyy',
                    'years' => range(date('Y') - 100, date('Y')),
                    'required' => true,
                ]
            )
            ->add(
                'city',
                'text',
                [
                    'attr' => [
                        'class' => 'form-control',
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
