<?php

declare(strict_types=1);

namespace Galop\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use AdminBundle\Entity\ApiUser;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

final class ApiUserAdmin extends AbstractAdmin
{
    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('id')
            ->add('firstname', null, ['label' => 'First Name'])
            ->add('lastname', null, ['label' => 'Last Name'])
            ->add('enabled','doctrine_orm_string', array(), 'choice',
                array('choices' => array('No' => '0', 'Yes' => '1'))
            )
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('id')
            ->add('firstname')
            ->add('lastname')
            ->add('createdAt', null, [
                'format' => 'Y-m-d H:i',
                'timezone' => 'Europe/Vaduz',
                'label' => 'Created'
            ])
            ->add('updatedAt', null, [
                'format' => 'Y-m-d H:i',
                'timezone' => 'Europe/Vaduz',
                'label' => 'Updated'
            ])
            ->add('enabled', null, ['editable' => true])
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ])
        ;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('firstname', 'text', array('label' => 'First Name'))
            ->add('lastname', 'text', array('label' => 'Last Name'))
            ->add('enabled')
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('firstname', null, ['label' => 'First name'])
            ->add('lastname', null, ['label' => 'Last name'])
            ->add('UserToken', null, ['label' => 'Client Key'])
            ->add('createdAt', null, [
                'format' => 'Y-m-d H:i',
                'timezone' => 'Europe/Vaduz',
                'label' => 'Created'
            ])
            ->add('updatedAt', null, [
                'format' => 'Y-m-d H:i',
                'timezone' => 'Europe/Vaduz',
                'label' => 'Updated'
            ])
            ->add('enabled')
        ;
    }
}
