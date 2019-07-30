<?php

declare(strict_types=1);

namespace Galop\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use AdminBundle\Entity\Events;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Intl\Intl;

final class EventsAdmin extends AbstractAdmin
{
    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('title')
            ->add('address')
            ->add('country')
            ->add('startdate', 'doctrine_orm_date_range')
            ->add('enddate', 'doctrine_orm_date_range')
            ->add('website')
            ->add('schedule')
            ->add('timetable')
            ->add('results')
            ->add('livestream')
            ->add('eventstatus','doctrine_orm_string', array(), 'choice',
                array('choices' => array('Online' => '0', 'Offline' => '1'))
            )
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('id')
            ->add('title')
            ->add('address')
            ->add('country', 'choice', array(
                'editable' => true,
                'class' => 'Vendor\ExampleBundle\Entity\ExampleStatus',
                'choices' => Intl::getRegionBundle()->getCountryNames(),
            ))
            ->add('startdate', null, [
                'format' => 'Y-m-d',
                'timezone' => 'Europe/Vaduz'
            ])
            ->add('enddate', null, [
                'format' => 'Y-m-d',
                'timezone' => 'Europe/Vaduz'
            ])
            ->add('website','url', [
                'attributes' => ['target' => '_blank'],
                'hide_protocol' => true,
            ])
            ->add('schedule','url', [
                'attributes' => ['target' => '_blank'],
                'hide_protocol' => true,
            ])
            ->add('timetable','url', [
                'attributes' => ['target' => '_blank'],
                'hide_protocol' => true,
            ])
            ->add('results','url', [
                'attributes' => ['target' => '_blank'],
                'hide_protocol' => true,
            ])
            ->add('livestream','url', [
                'attributes' => ['target' => '_blank'],
                'hide_protocol' => true,
            ])
            ->add('createdAt', null, [
                'format' => 'Y-m-d H:i',
                'timezone' => 'Europe/Vaduz',
                'label' => 'Created'
            ])
            ->add('eventstatus', 'choice', [
                'editable' => true,
                'class' => 'Vendor\ExampleBundle\Entity\ExampleStatus',
                'choices' => [
                    0 => 'Online',
                    1 => 'Offline',
                ],
            ])
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
            ->add('title', 'text', array('label' => 'Title', 'required' => true))
            ->add('address', 'textarea', array('label' => 'Address', 'required' => true))
            ->add('country', CountryType::class,[
                'placeholder' => 'Select Country',
            ],array('required' => true))
            ->add('startdate', DateType::class, [
                'label' => 'Startdate',
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('enddate', DateType::class, [
                'label' => 'Enddate',
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('website', UrlType::class, [
                'label' => 'Website',
            ])
            ->add('schedule', UrlType::class, [
                'label' => 'Schedule',
            ])
            ->add('timetable', UrlType::class, [
                'label' => 'Time Table',
            ])
            ->add('results', UrlType::class, [
                'label' => 'Results',
            ])
            ->add('livestream', UrlType::class, [
                'label' => 'Live Stream',
            ])
            ->add('eventstatus', ChoiceType::class, [
                'choices' => [
                    'Online' => '0',
                    'Offline' => '1',
                ],
            ])
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('title')
            ->add('address')
            ->add('country', 'choice', [
                'class' => 'Vendor\ExampleBundle\Entity\ExampleStatus',
                'choices' => Intl::getRegionBundle()->getCountryNames(),
            ])
            ->add('startdate')
            ->add('enddate')
            ->add('website')
            ->add('schedule')
            ->add('timetable')
            ->add('results')
            ->add('livestream')
            ->add('createdAt', null, ['label' => 'Created'])

            ->add('eventstatus', 'choice', [
                'class' => 'Vendor\ExampleBundle\Entity\ExampleStatus',
                'choices' => [
                    0 => 'Online',
                    1 => 'Offline',
                ],
            ])
        ;
    }
}
