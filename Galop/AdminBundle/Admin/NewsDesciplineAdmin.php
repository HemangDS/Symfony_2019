<?php

namespace Galop\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Route\RouteCollection;


class NewsDesciplineAdmin extends AbstractAdmin
{
	
	/**
     * @param FormMapper $formMapper
     */
	protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab('General')
                ->with('News Discipline Data', ['class' => 'col-md-9'])->end()
            ->end()
        ;

        $formMapper
            ->tab('General')
                ->with('News Discipline Data')
                    ->add('descipline', 'text', array('label' => 'Name'))
                ->end()
            ->end()
        ;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
                ->add('descipline', null, array('label' => 'Name'))
                ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
                ->add('descipline', null, array('label' => 'Name'))
                ->add('_action', 'actions', array(
                    'actions' => array(
                        'show' => array(),
                        /*'edit' => array(),
                        'delete' => array(),*/
                    ),
                ))
                ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->tab('General')
                ->with('News Discipline Data')
                    ->add('descipline', null, array('label' => 'Name'))
                ->end()
            ->end()
        ;
    }

    public function configureRoutes(RouteCollection $collection)
    {
      $collection->remove('create');
      $collection->remove('edit');
      $collection->remove('delete');
    }
}