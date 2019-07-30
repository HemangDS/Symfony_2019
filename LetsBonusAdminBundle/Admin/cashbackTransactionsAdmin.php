<?php

namespace iFlair\LetsBonusAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class cashbackTransactionsAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('shopId')
            ->add('userId')
            ->add('transactionId')
            ->add('networkId')
            ->add('amount')
            ->add('affiliateAmount')
            ->add('totalAffiliateAmount')
            ->add('letsbonusPct')
            ->add('extraAmount')
            ->add('extraPct')
            ->add('currency')
            ->add('orderReference')
            ->add('aprovalDate')
            ->add('date')
            ->add('userName')
            ->add('userAddress')
            ->add('userDni')
            ->add('userPhone')
            ->add('userBankAccountNumber')
            ->add('bic')
            ->add('companyId')
            ->add('cashbacktransactionsChilds')
            ->add('adminuserId')
            ->add('manualNumdaystoapprove')
            ->add('comments')
            ->add('parentTransactionId')
            ->add('cashbacksettingId')
            ->add('sepageneratedbyUserId')
            ->add('sepageneratedDate')
            ->add('deviceType')
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
                    'doubleCashBack' => array('template' => 'iFlairLetsBonusAdminBundle:CashbackTransactions:doubleCashBack_action_template.html.twig'),
                ),
            ))
            ->add('id')
            ->add('shopId')
            ->add('userId')
            ->add('networkId')
            ->add('amount')
            ->add('currency')
            ->add('status')
            ->add('type')
            ->add('date', 'datetime', array('label' => 'Start date', 'pattern' => 'yyyy-MM-dd hh:mm:ss', 'locale' => 'en', 'timezone' => 'Europe/Paris'))
            ->add('created', 'datetime', array('label' => 'Start date', 'pattern' => 'yyyy-MM-dd hh:mm:ss', 'locale' => 'en', 'timezone' => 'Europe/Paris'))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('shopId', 'entity', array(
                'class' => 'iFlair\LetsBonusAdminBundle\Entity\Shop',
                'property' => 'id',
            ))
            ->add('userId')
            ->add('transactionId')
            ->add('networkId', 'entity', array(
                'class' => 'iFlair\LetsBonusAdminBundle\Entity\Network',
                'property' => 'name',
            ))
            ->add('amount')
            ->add('affiliateAmount')
            ->add('totalAffiliateAmount')
            ->add('letsbonusPct')
            ->add('extraAmount')
            ->add('extraPct')
            ->add('currency', 'entity', array(
                'class' => 'iFlair\LetsBonusAdminBundle\Entity\Currency',
                'property' => 'code',
            ))
            ->add('status', 'choice', array('choices' => array('approved' => 'Approved', 'pending' => 'Pending', 'cancelled' => 'Cancelled', 'paid' => 'Paid')))
            ->add('type')
            ->add('networkStatus', 'choice', array('choices' => array('0' => 'Deactivated', '1' => 'Activated')))
            ->add('orderReference')
            ->add('affiliateAproveddate')
            ->add('affiliateCanceldate')
            ->add('aprovalDate')
            ->add('date')
            ->add('userName')
            ->add('userAddress')
            ->add('userDni')
            ->add('userPhone')
            ->add('userBankAccountNumber')
            ->add('bic')
            ->add('companyId', 'entity', array(
                'class' => 'iFlair\LetsBonusAdminBundle\Entity\Companies',
                'property' => 'name',
            ))
            ->add('cashbacktransactionsChilds')
            ->add('adminuserId')
            ->add('manualNumdaystoapprove')
            ->add('comments')
            ->add('parentTransactionId')
            ->add('cashbacksettingId', 'entity', array(
                'class' => 'iFlair\LetsBonusAdminBundle\Entity\cashbackSettings',
                'property' => 'name',
            ))
            ->add('sepageneratedbyUserId')
            ->add('sepageneratedDate')
            ->add('deviceType')
            ->add('created')
            ->add('modified')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('shopId')
            ->add('userId')
            ->add('transactionId')
            ->add('networkId')
            ->add('amount')
            ->add('affiliateAmount')
            ->add('totalAffiliateAmount')
            ->add('letsbonusPct')
            ->add('extraAmount')
            ->add('extraPct')
            ->add('currency')
            ->add('status')
            ->add('type')
            ->add('networkStatus')
            ->add('orderReference')
            ->add('affiliateAproveddate')
            ->add('affiliateCanceldate')
            ->add('aprovalDate')
            ->add('date')
            ->add('userName')
            ->add('userAddress')
            ->add('userDni')
            ->add('userPhone')
            ->add('userBankAccountNumber')
            ->add('bic')
            ->add('companyId')
            ->add('cashbacktransactionsChilds')
            ->add('adminuserId')
            ->add('manualNumdaystoapprove')
            ->add('comments')
            ->add('parentTransactionId')
            ->add('cashbacksettingId')
            ->add('sepageneratedbyUserId')
            ->add('sepageneratedDate')
            ->add('deviceType')
            ->add('created')
            ->add('modified')
        ;
    }

    protected function configureRoutes(\Sonata\AdminBundle\Route\RouteCollection $collection)
    {
        $collection->add('doubleCashBack', $this->getRouterIdParameter().'/double/cachback');
    }
    public function postPersist($object)
    {
        $this->getRequest()->getSession()->getFlashBag()->add('mytodo_success', 'My To-Do custom success message');
        $this->getRequest()->getSession()->getFlashBag()->add('success', 'My To-Do custom success message');
    }
}
