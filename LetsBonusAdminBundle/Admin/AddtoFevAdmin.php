<?php

namespace iFlair\LetsBonusAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
// use Sonata\AdminBundle\Form\FormMapper;
// use Sonata\AdminBundle\Show\ShowMapper;

use Sonata\AdminBundle\Route\RouteCollection;
// USED for getting user data
use iFlair\LetsBonusFrontBundle\Entity\AddtoFev;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class AddtoFevAdmin extends Admin
{
    protected $baseRoutePattern = 'addtofev';

    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection
        ->remove('create')
        ->remove('export')
        ;
    }
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('userId')
            ->add('shopId')
            ->add('shopHistoryId')
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
                  ///  'edit' => array(),
                    'delete' => array(),
                ),
            ))
            ->add('id')
            ->addIdentifier('userId.name', null,  array('label' => 'Customer name'))
            ->addIdentifier('shopId.id', null,  array('label' => 'Shop id'))
            ->addIdentifier('shopHistoryId.title', null,  array('label' => 'Shop title'))
            //->add('created')
            //->add('modified')           
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('userId', 'entity', array(
                'class' => 'iFlair\LetsBonusAdminBundle\Entity\FrontUser',
                'property' => 'name',
            ))
            ->add('shopId', 'entity', array(
                'class' => 'iFlair\LetsBonusAdminBundle\Entity\Shop',
                'property' => 'keywords',
            ))
            ->add('shopHistoryId', 'entity', array(
                'class' => 'iFlair\LetsBonusAdminBundle\Entity\shopHistory',
                'property' => 'title',
            ))
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
            ->add('userId')
            ->add('shopId')
            ->add('shopHistoryId')
            ->add('created')
            ->add('modified')
        ;
    }
}
