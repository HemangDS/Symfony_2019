<?php

namespace iFlair\LetsBonusAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class PromoAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('companies')
            ->add('shop')
            ->add('daysToApprove')
            ->add('transactionAmount')
            ->add('users')
            ->add('comments')
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
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                ),
            ))
            ->add('id')
            ->add('companies')
            ->add('shop')
            ->add('daysToApprove')
            ->add('transactionAmount')
            ->add('users')
            ->add('comments')
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
            ->add('companies', 'entity', array(
                'class' => 'iFlair\LetsBonusAdminBundle\Entity\Companies',
                'required' => true,
                'property' => 'name',
                'empty_value' => '-- Choose Companies --',
            ))
            ->add('shop', 'shtumi_dependent_filtered_entity', array(
                'entity_alias' => 'region_by_country',
                'empty_value' => '-- Choose shop --',
                'parent_field' => 'companies', ))
            ->add('daysToApprove')
            ->add('transactionAmount')
            ->add('users')
            ->add('comments')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('companies')
            ->add('shop')
            ->add('daysToApprove')
            ->add('transactionAmount')
            ->add('users')
            ->add('comments')
            ->add('created')
            ->add('modified')
        ;
    }
}
