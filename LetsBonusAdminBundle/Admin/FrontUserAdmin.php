<?php

namespace iFlair\LetsBonusAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class FrontUserAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name')
            ->add('surname')
            ->add('alias')
            ->add('email')
            ->add('password')
            ->add('isShoppiday')
            ->add('apiFlag')
            ->add('enabled')
            ->add('companyId')
            ->add('userCreateDate')
            ->add('userType')
            ->add('userGender')
            ->add('isSubscribed')
            ->add('userBirthDate')
            ->add('city')
            ->add('loginType')
            ->add('facebookId')
            ->add('googleId')
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
                )
            ))
            ->add('id')
            ->add('name')
            ->add('surname')
            ->add('alias')
            ->add('email')
            ->add('isShoppiday')
            ->add('enabled')
            ->add('isSubscribed')
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
            ->add('surname')
            ->add('alias')
            ->add('email')
            ->add('password')
            ->add('isShoppiday')
            ->add('apiFlag')
            ->add('enabled')
            ->add('companyId')
            ->add('userCreateDate',   'sonata_type_datetime_picker', array('label' => 'userCreateDate', 'dp_language' => 'en', 'format' => 'yyyy-MM-dd hh:mm:ss', 'read_only' => true))
            ->add('userType')
            ->add('userGender')
            ->add('isSubscribed')
            ->add('userBirthDate',   'sonata_type_datetime_picker', array('label' => 'userBirthDate', 'dp_language' => 'en', 'format' => 'yyyy-MM-dd hh:mm:ss', 'read_only' => true))
            ->add('city')
            ->add('loginType')
            ->add('facebookId')
            ->add('googleId')
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
            ->add('surname')
            ->add('alias')
            ->add('email')
            ->add('password')
            ->add('isShoppiday')
            ->add('apiFlag')
            ->add('enabled')
            ->add('companyId')
            ->add('userCreateDate')
            ->add('userType')
            ->add('userGender')
            ->add('isSubscribed')
            ->add('userBirthDate')
            ->add('city')
            ->add('loginType')
            ->add('facebookId')
            ->add('googleId')
            ->add('created')
            ->add('modified')
        ;
    }
}
