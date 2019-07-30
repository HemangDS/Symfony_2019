<?php

namespace iFlair\LetsBonusAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class VoucherTradeDoublerSiteTokenAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('siteId')
            ->add('siteName')
            ->add('siteToken')
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
            ->add('company', 'entity', array(
                'class' => 'iFlair\LetsBonusAdminBundle\Entity\Companies',
                'property' => 'name',
            ))
            ->add('siteId')
            ->add('siteName')
            ->add('siteToken')
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('company', 'entity', array(
                'class' => 'iFlair\LetsBonusAdminBundle\Entity\Companies',
                'property' => 'name',
            ))
            ->add('siteId')
            ->add('siteName')
            ->add('siteToken')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('siteId')
            ->add('siteName')
            ->add('siteToken')
        ;
    }
}
