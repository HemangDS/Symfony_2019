<?php

namespace iFlair\LetsBonusAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class AdvertisementTypeAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('advTypeName')
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
            ->add('advTypeName')
            ->add('created', 'datetime', array('label' => 'Created', 'pattern' => 'yyyy-MM-dd hh:mm:ss', 'locale' => 'en'))
            ->add('modified', 'datetime', array('label' => 'Modified', 'pattern' => 'yyyy-MM-dd hh:mm:ss', 'locale' => 'en'))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('advTypeName')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('advTypeName')
        ;
    }

    /**
     * Default Datagrid values.
     *
     * @var array
     */
    protected $datagridValues = array(
        '_per_page' => 1,
        '_page' => 1,
        '_sort_order' => 'DESC', // reverse order (default = 'ASC')
        '_sort_by' => 'created',  // name of the ordered field
        // (default = the model's id field, if any)

        // the '_sort_by' key can be of the form 'mySubModel.mySubSubModel.myField'.
    );
}
