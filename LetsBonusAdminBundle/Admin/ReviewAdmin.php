<?php

namespace iFlair\LetsBonusAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
// use Sonata\AdminBundle\Form\FormMapper;
// use Sonata\AdminBundle\Show\ShowMapper;

use Sonata\AdminBundle\Route\RouteCollection;
// USED for getting user data
use iFlair\LetsBonusFrontBundle\Entity\Review;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class ReviewAdmin extends Admin
{
    protected $baseRoutePattern = 'review';

    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection
        // ->add('sepa_list', 'sepa-list')
        ->add('makepayment', 'make-payment')
        ->remove('create')
        // ->remove('batch')        
        ->remove('export')
        //->remove('delete')
        // ->remove('edit')
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
            ->add('username')
            ->add('email')
            ->add('review')
            ->add('rating')
            ->add('created')
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
                 //   'edit' => array(),
                    'delete' => array(),
                ),
            ))
            ->add('id')
            ->add('username')
            ->add('email')
             ->addIdentifier('shopId.id', null,  array('label' => 'Shop id'))
            ->addIdentifier('shopHistoryId.title', null,  array('label' => 'Shop title'))
            ->add('review')
            ->add('rating')
            ->add('created')
        ;
   }

    /*
     * @param FormMapper $formMapper
     */
    // protected function configureFormFields(FormMapper $formMapper)
    // {
    //   
    // }
    /*
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
              ->add('username')
            ->add('email')
            ->add('shopId')
            ->add('shopHistoryId')
            ->add('review')
            ->add('rating')
            ->add('created')
        ;
    }
}
