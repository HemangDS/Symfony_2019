<?php

namespace iFlair\LetsBonusAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class CollectionAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name')
            ->add('url')
            ->add('status')
            ->add('show_in_front')
            ->add('mark_special')
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
                ),
            ))
            ->add('id')
            ->add('name')
            ->add('url')
            ->add('status')
            ->add('show_in_front')
            ->add('mark_special')
            ->add('created')
            ->add('modified')
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
            ->add('url')
            ->add('status', 'choice', array('choices' => array('0' => 'disabled', '1' => 'enabled')))
            ->add('show_in_front', 'choice', array('choices' => array('0' => 'disabled', '1' => 'enabled')))
            ->add('mark_special', 'choice', array('choices' => array('0' => 'disabled', '1' => 'enabled')))
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('name')
            ->add('url')
            ->add('status')
            ->add('show_in_front')
            ->add('mark_special')
            ->add('created')
            ->add('modified')
        ;
    }
    public function getBatchActions()
    {
        // retrieve the default batch actions (currently only delete)
        $actions = parent::getBatchActions();

        if (
          $this->hasRoute('edit') && $this->isGranted('EDIT') &&
          $this->hasRoute('delete') && $this->isGranted('DELETE')
        ) {
            $actions['merge'] = array(
                'label' => 'Edit Custom',
                'translation_domain' => 'SonataAdminBundle',
                'ask_confirmation' => true,
            );
        }

        return $actions;
    }
}
