<?php

namespace iFlair\LetsBonusFrontBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('username', 'text', array('data' => '', 'attr' => array(
                                      'class' => 'form-control',
                                  )))
            ->add('email', 'email', array('data' => '', 'attr' => array(
                                      'class' => 'form-control',
                                  )))

            ->add('review', 'textarea', array('attr' => array('class' => 'form-control', 'rows' => '10', 'cols' => '10')))

            ->add('rating', 'choice', array(
                    'choices' => array(
                        '1' => '1',
                        '2' => '2',
                        '3' => '3',
                        '4' => '4',
                        '5' => '5',
                    ), 'expanded' => true,
                     'multiple' => false,
                ))
          ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'iFlair\LetsBonusFrontBundle\Entity\Review',
        ));
    }

    public function getName()
    {
        return '';
    }
}
