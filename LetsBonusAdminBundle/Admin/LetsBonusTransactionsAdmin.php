<?php

namespace iFlair\LetsBonusAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class LetsBonusTransactionsAdmin extends Admin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection
        // ->add('sepa_list', 'sepa-list')
        //->add('makepayment', 'make-payment')
        ->remove('create')
        // ->remove('batch')        
        ->remove('export')
        ->remove('delete')
        ->remove('edit')
        // ->remove('list')
        // ->remove('show')
        ;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('transactionId')
            ->add('referenceId')
            ->add('amount')
            ->add('totalPurchase')
            ->add('commission')
            ->add('transactionDate')
            ->add('internalStatus')
            ->add('affiliateStatus')
            ->add('affiliateConfirmDate')
            ->add('network')
            ->add('shop')
            //->add('user')
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
                ),
            ))
            ->add('id')
            ->add('transactionId')
            ->add('referenceId')
            ->add('amount')
            ->add('totalPurchase')
            ->add('commission')
            ->add('transactionDate')
            ->add('internalStatus')
            ->add('affiliateStatus')
            ->add('affiliateConfirmDate')
            ->add('network')
            ->add('shop')
            //->add('user')            
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('id')
            ->add('transactionId')
            ->add('referenceId')
            ->add('amount')
            ->add('totalPurchase')
            ->add('commission')
            ->add('transactionDate')
            ->add('internalStatus')
            ->add('affiliateStatus')
            ->add('affiliateConfirmDate')
            ->add('network')
            ->add('shop')
            ->add('user')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('transactionId')
            ->add('referenceId')
            ->add('amount')
            ->add('totalPurchase')
            ->add('commission')
            ->add('transactionDate')
            ->add('internalStatus')
            ->add('affiliateStatus')
            ->add('affiliateConfirmDate')
            ->add('network')
            ->add('shop')
            ->add('user')
        ;
    }
}
