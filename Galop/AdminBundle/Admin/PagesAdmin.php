<?php

namespace Galop\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use FOS\CKEditorBundle\Form\Type\CKEditorType;


class PagesAdmin extends AbstractAdmin
{
	
	/**
     * @param FormMapper $formMapper
     */
	protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab('General')
                ->with('Pages Data', ['class' => 'col-md-9'])->end()
            ->end()
        ;

        $formMapper
            ->tab('General')
                ->with('Pages Data')
                    ->add('title', 'text', array('label' => 'Title')) 
                    ->add('metaTitle', 'text', array('label' => 'Meta Title','required' => false)) 
                    ->add('metaDescription', 'text', array('label' => 'Meta Description','required' => false))
                    ->add('content', CKEditorType::class, array(
                                'label' => 'Content',
                            )
                        )
                    ->add('isActive', null, array('label' => 'Is Active?'))
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
                ->add('title', null, array('label' => 'Title'))
                ->add('urlKey', null, array('label' => 'Url Key'))
                ->add('isActive')
                ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
                ->add('title', null, array('label' => 'Title'))
                ->add('urlKey', null, array('label' => 'Url Key'))
                ->add('isActive')
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
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('title') 
            ->add('metaTitle') 
            ->add('metaDescription') 
            ->add('urlKey') 
            ->add('content')
            ->add('isActive', null, array('label' => 'Is Active?'))
        ;
    }
}