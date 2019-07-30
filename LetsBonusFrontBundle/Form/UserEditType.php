<?php

namespace iFlair\LetsBonusFrontBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserEditType extends AbstractType
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
                    'disabled' => true,
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
                'userBirthDate',
                'date',
                [
                    'label' => 'Expiration date',
                    'widget' => 'choice',
                    'empty_value' => ['year' => 'YYYY', 'month' => 'MM', 'day' => 'DD'],
                    'format' => 'ddMMyyyy',
                    'years' => range(date('Y') - 100, date('Y') - 18),
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
            )
            ->add(
                'alias',
                'text',
                [
                    'required' => false,
                    'attr' => [
                        'class' => 'form-control',
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
