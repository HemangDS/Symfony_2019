<?php

namespace iFlair\LetsBonusAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class CmsCareersAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('title')
            ->add('description')
            // ->add('created')
            // ->add('modified')
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
            ->add('title')
            ->add('description')
            // ->add('created', 'datetime', array('label'=>'Created', 'pattern' => 'yyyy-mm-dd hh:mm:ss','locale' => 'en','timezone' => 'Europe/Paris'))
            // ->add('modified', 'datetime', array('label'=>'Modified', 'pattern' => 'yyyy-mm-dd hh:mm:ss','locale' => 'en','timezone' => 'Europe/Paris'))            
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            // ->add('id')
            ->add('title')
            ->add('description', 'textarea', array('attr' => array('class' => 'ckeditor')))
            // ->add('logo', 'sonata_media_type', array(
            //     'provider' => 'sonata.media.provider.image',
            //     'context'  => 'default',
            // ))
            // ->add('shop', 'entity', array(
            //     'class' => 'iFlair\LetsBonusAdminBundle\Entity\shop',
            //     'property' => 'id',
            //     'multiple' => true,
            //     'expanded' => true
            // ))
            ->add('status', 'choice', array('label' => 'Status', 'choices' => array('0' => 'disabled', '1' => 'enabled')))
            // ->add('showinfront', 'choice', array('label'=>'Show in front ?', 'choices' => array('0' => 'No', '1' => 'Yes')))
            // ->add('startDate', 'sonata_type_datetime_picker', array('label'=>'Start date', 'dp_language' => 'en','format' => 'yyyy-MM-dd hh:mm:ss', 'read_only' => true))
            // ->add('endDate',   'sonata_type_datetime_picker', array('label'=>'End date'  , 'dp_language' => 'en','format' => 'yyyy-MM-dd hh:mm:ss', 'read_only' => true))
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('title')
            ->add('description')
            /*->add('shop')
            ->add('status')
            ->add('showinfront')
            ->add('startDate', 'datetime', array('label'=>'Start date', 'pattern' => 'yyyy-mm-dd hh:mm:ss','locale' => 'en','timezone' => 'Europe/Paris'))
            ->add('endDate', 'datetime', array('label'=>'End date', 'pattern' => 'yyyy-mm-dd hh:mm:ss','locale' => 'en','timezone' => 'Europe/Paris'))*/
        ;
    }
}
