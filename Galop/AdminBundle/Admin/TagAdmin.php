<?php

namespace Galop\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\Type\ModelType;



class TagAdmin extends AbstractAdmin
{
	
	/**
     * @param FormMapper $formMapper
     */
	protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab('General')
                ->with('Tag Data', ['class' => 'col-md-9'])->end()
            ->end()
        ;

        $formMapper
            ->tab('General')
                ->with('Tag Data')
                    ->add('title', 'text', array('label' => 'Title'))
                    ->add('group', 'sonata_type_model_autocomplete', [
                            'class' => 'Galop\AdminBundle\Entity\TagGroup',
                            'property' => 'title',
                            'multiple' => true,
                            'placeholder'=> 'Select Group'
                        ]
                    )
                    ->add('status', null, array('label' => 'Status'))
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
                ->add('group', null, array('label' => 'Group'))
                ->add('status')
                ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
                ->add('title', null, array('label' => 'Title'))
                ->add('group', null, array('label' => 'Group'))
                ->add('status')
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
            ->add('title', null, array('label' => 'Title'))
            ->add('group', null, array('label' => 'Group'))
            ->add('status')
        ;
    }
}