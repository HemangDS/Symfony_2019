<?php

namespace iFlair\LetsBonusAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class CountryAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name')
            ->add('code')
            ->add('language')
            ->add('created')
            ->add('modified')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('name')
            ->add('code')
            ->add('language')
            ->add('created')
            ->add('modified')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                ),
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
            ->add('code')
            ->add('language', 'entity', array(
                'class' => 'iFlair\LetsBonusAdminBundle\Entity\Language',
                'property' => 'name',
            ))
            ->add('domain', 'sonata_type_model_autocomplete', array(
                    'property' => 'name',
                    'label' => 'Select domains',
                    'class' => 'iFlairLetsBonusAdminBundle:Domain',
                    'multiple' => true,
                    'by_reference' => true,
                    'attr' => ['style' => 'width: 100%;'],
                    'required' => true,
                    'to_string_callback' => function ($entity) {
                        return $entity->getName();
                    },
                )
            )
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('name')
            ->add('code')
            ->add('language')
            ->add('created')
            ->add('modified')
        ;
    }
}
