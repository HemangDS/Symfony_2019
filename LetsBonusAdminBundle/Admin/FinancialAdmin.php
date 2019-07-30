<?php

namespace iFlair\LetsBonusAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
// use Sonata\AdminBundle\Form\FormMapper;
// use Sonata\AdminBundle\Show\ShowMapper;

use Sonata\AdminBundle\Route\RouteCollection;
// USED for getting user data
use iFlair\LetsBonusAdminBundle\Entity\User;

class FinancialAdmin extends Admin
{
    protected $baseRoutePattern = 'financial';

    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection
        // ->add('sepa_list', 'sepa-list')
        // ->add('makepayment', 'make-payment')
        ->add('newlist', 'newlist')
        ->add('revert', 'revert')
        ->remove('create')
        // ->remove('batch')        
        ->remove('export')
        ->remove('delete')
        // ->remove('edit')
        // ->remove('list')
        // ->remove('show')
        ;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    // protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    // {
    //     $datagridMapper
    //         ->add('id')
    //         ->add('userId') // get user name also from here :: USERNAME REMAINING
    //         ->add('username')
    //         ->add('userName') // account hoder name
    //         ->add('userBankAccountNumber')
    //         ->add('bic')
    //         ->add('amount')
    //         ->add('currency') // currency id :: get currency name from here            
    //         ->add('sepageneratedDate')
    //         ->add('sepageneratedbyUserId')
    //     ;
    // }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        /*$listMapper
            ->add('id',null,array(
                'sortable'=>false))
            ->add('userId',null,array(
                'sortable'=>false)) // get user name also from here :: USERNAME REMAINING
            
            ->add('username', 'entity', array(
                'class' => 'iFlair\LetsBonusAdminBundle\Entity\User',
                'property' => 'username',
                'sortable'=>false,
                'label' => 'User'
            ))

            ->add('userName',null,array(
                'sortable'=>false,'label' => 'Titular cuenta')) // account hoder name 
            ->add('userBankAccountNumber',null,array(
                'sortable'=>false,'label' => 'IBAN'))
            ->add('bic',null,array(
                'sortable'=>false,'label' => 'BIC'))
            ->add('amount',null,array(
                'sortable'=>false))
            ->add('currency',null,array(
                'sortable'=>false)) // currency id :: get currency name from here            
            ->add('sepageneratedDate',null,array(
                'sortable'=>false,'label' => 'Fecha solicitud'))
            ->add('sepageneratedbyUserId',null,array(
                'sortable'=>false,'label' => 'Sepa creado'))            
        ;*/
    }
    /*
     * @param FormMapper $formMapper
     */
    // protected function configureFormFields(FormMapper $formMapper)
    // {
    //     $formMapper
    //         ->add('id')
    //         ->add('userId') // get user name also from here :: USERNAME REMAINING
    //         ->add('userName') // account hoder name 
    //         ->add('AccountHolderName') // account hoder name 
    //         ->add('userBankAccountNumber')
    //         ->add('bic')
    //         ->add('amount')
    //         ->add('currency') // currency id :: get currency name from here            
    //         ->add('sepageneratedDate')
    //         ->add('sepageneratedbyUserId')
    //     ;
    // }
    /*
     * @param ShowMapper $showMapper
     */
    // protected function configureShowFields(ShowMapper $showMapper)
    // {
    //     $showMapper
    //         ->add('ID')            
    //         ->add('User id') // get user name also from here :: USERNAME REMAINING
    //         ->add('Titular cuenta') // account hoder name
    //         ->add('IBAN',null,array('sortable'=>true))
    //         ->add('BIC')
    //         ->add('Amount')
    //         ->add('Currency') // currency id :: get currency name from here
    //         ->add('Fecha solicitud')
    //         ->add('Sepa creado')
    //     ;
    // }
}
