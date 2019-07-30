<?php

namespace iFlair\LetsBonusAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class CompaniesAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name')
            ->add('currency')
            ->add('isoCode')
            ->add('lang')
            ->add('commonConditions')
            ->add('hoursOffset')
            ->add('timezone')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                ),
            ))
            ->add('id')
            ->add('name')
            ->add('currency')
            ->add('isoCode')
            ->add('timezone')
            ->add('created')
            ->add('modified')
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
            ->add('currency')
            ->add('currency', 'entity', array(
                'class' => 'iFlair\LetsBonusAdminBundle\Entity\Currency',
                'property' => 'code',
            ))
            ->add('isoCode')
            ->add('lang', 'entity', array(
                'class' => 'iFlair\LetsBonusAdminBundle\Entity\Language',
                'property' => 'code',
            ))
            ->add('commonConditions')
            ->add('hoursOffset')
            ->add('timezone')
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
            ->add('currency')
            ->add('isoCode')
            ->add('lang')
            ->add('commonConditions')
            ->add('hoursOffset')
            ->add('timezone')
        ;
    }
}
