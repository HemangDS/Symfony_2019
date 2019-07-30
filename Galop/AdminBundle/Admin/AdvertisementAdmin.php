<?php
declare(strict_types=1);

namespace Galop\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use AdminBundle\Entity\Advertisement;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;


final class AdvertisementAdmin extends AbstractAdmin
{
    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('id')
            ->add('title')
            ->add('userid')
            ->add('startdate', 'doctrine_orm_date_range')
            ->add('enddate', 'doctrine_orm_date_range')
            ->add('status')
            ->add('link')
            ->add('zone')
            ->add('ClientName', null, ['label' => 'Client Name'])
            ->add('price')
            ->add('remarks')
            ->add('DesignBypweb', null, ['label' => 'Design-By-Pweb'])
            ->add('ReminderDate', null, ['label' => 'Reminder Date'])
        ;
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $datagridMapper
                ->add('invoiced')    
            ;     
        }
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('id')
            ->add('title')
            ->add('userid', null, ['label' => 'Username'])
             ->add('updatedByUser', null, ['label' => 'Updated-By'])
            ->add('createdAt', null, [
                'format' => 'Y-m-d H:i',
                'timezone' => 'Europe/Vaduz',
                'label' => 'Created'
            ])
            ->add('startdate', null, [
                'format' => 'Y-m-d',
                'timezone' => 'Europe/Vaduz'
            ])
            ->add('enddate', null, [
                'format' => 'Y-m-d',
                'timezone' => 'Europe/Vaduz'
            ])
            ->add('status')
            ->add('desktop_views', null, ['label' => 'Desktop Views'])
            ->add('mobile_views', null, ['label' => 'Mobile Views'])
            ->add('tablet_views', null, ['label' => 'Tablet Views'])
            ->add('desktop_counter', null, ['label' => 'Desktop Clicks'])
            ->add('mobile_counter', null, ['label' => 'Mobile Clicks'])
            ->add('tablet_counter', null, ['label' => 'Tablet Clicks'])
            ->add('link','url', [
                'attributes' => ['target' => '_blank'],
                'hide_protocol' => true,
            ])
            ->add('zone', null, ['label' => 'Zone'])
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
            ->tab('General')
                ->with('Advertisement Data', ['class' => 'col-md-9'])->end()
            ->end()
        ;

        $formMapper
            ->tab('Image English')
                ->with('English', ['class' => 'col-md-6'])->end()
            ->end()
        ;

        $formMapper
            ->tab('Image Dutch')
                ->with('Dutch', ['class' => 'col-md-6'])->end()
            ->end()
        ;

        $formMapper
            ->tab('Image French')
                ->with('French', ['class' => 'col-md-6'])->end()
            ->end()
        ;

        $formMapper
            ->tab('Client Information')
                ->with('For administration', ['class' => 'col-md-6'])->end()
            ->end()
        ;

        $formMapper
            ->tab('General')
                ->with('Advertisement Data')   
                    ->add('title', 'text', array('label' => 'Title'))
                    ->add('startdate', DateType::class, [
                        'label' => 'Startdate',
                        'widget' => 'single_text',
                    ])
                    ->add('enddate', DateType::class, [
                        'label' => 'Enddate',
                        'widget' => 'single_text',
                    ])
                    ->add('link', 'text', array('label' => 'Link'))
                    ->add('zone', 'entity', [
                        'class'    => 'Galop\AdminBundle\Entity\AdvertisementZone',
                        'attr' => ['class' => 'zonecls']
                    ])
                    ->add('name', 'text', array('label' => 'Name', 'required' => false))
                    ->add('address', 'textarea', array('label' => 'Address', 'required' => false))
                    ->add('ZipCode', 'number', array('label' => 'Zip Code', 'required' => false))
                    ->add('city', 'text', array('label' => 'City', 'required' => false))
                    ->add('country', CountryType::class,[
                        'required' => false,
                        'placeholder' => 'Select Country',
                    ])
                    ->add('phoneNumber', 'number', array('label' => 'Phone Number', 'required' => false))
                    ->add('email', 'email', array('label' => 'Email', 'required' => false))
                    ->add('status', null, array('label' => 'Status'))
                    ->add('desktop_counter', 'integer', array(
                            'label' => 'Desktop Clicks',
                            'required' => false,
                            'attr' => array(
                                'readonly' => true
                            )
                        )
                    )
                    ->add('mobile_counter', 'integer', array(
                            'label' => 'Mobile Clicks',
                            'required' => false,
                            'attr' => array(
                                'readonly' => true
                            )
                        )
                    )
                    ->add('tablet_counter', 'integer', array(
                            'label' => 'Tablet Clicks',
                            'required' => false,
                            'attr' => array(
                                'readonly' => true
                            )
                        )
                    )
                ->end()
            ->end()
        ;
        $formMapper    
            ->tab('Image English')
                ->with('English')   
                    ->add('EngDesktopAdd', 'sonata_type_model_list', array('label' => 'Desktop Image'), array(
                        'edit' => 'inline',
                        'inline' => 'table',
                        'sortable' => 'position',
                        'link_parameters' => array('provider' => 'sonata.media.provider.image'),
                        'context' => 'default',
                        'admin_code' => 'sonata.media.admin.media',
                    ))
                    ->add('EngMobileAdd', 'sonata_type_model_list', array(
                        'label' => 'Mobile Image'), array(
                        'edit' => 'inline',
                        'inline' => 'table',
                        'sortable' => 'position',
                        'link_parameters' => array('provider' => 'sonata.media.provider.image'),
                        'context' => 'default',
                        'admin_code' => 'sonata.media.admin.media',
                    ))
                    ->add('EngTabletAdd', 'sonata_type_model_list',array('label' => 'Tablet Image'), array(
                        'edit' => 'inline',
                        'inline' => 'table',
                        'sortable' => 'position',
                        'link_parameters' => array('provider' => 'sonata.media.provider.image'),
                        'context' => 'default',
                        'admin_code' => 'sonata.media.admin.media',
                    ))
                ->end()
            ->end()    
        ;
         $formMapper    
            ->tab('Image Dutch')
                ->with('Dutch')   
                    ->add('DutchDesktopAdd', 'sonata_type_model_list', array('label' => 'Desktop Image'), array(
                        'edit' => 'inline',
                        'inline' => 'table',
                        'sortable' => 'position',
                        'link_parameters' => array('provider' => 'sonata.media.provider.image'),
                        'context' => 'default',
                        'admin_code' => 'sonata.media.admin.media',
                    ))
                    ->add('DutchMobileAdd', 'sonata_type_model_list', array('label' => 'Mobile Image'), array(
                        'edit' => 'inline',
                        'inline' => 'table',
                        'sortable' => 'position',
                        'link_parameters' => array('provider' => 'sonata.media.provider.image'),
                        'context' => 'default',
                        'admin_code' => 'sonata.media.admin.media',
                    ))
                    ->add('DutchTabletAdd', 'sonata_type_model_list', array('label' => 'Tablet Image'), array(
                        'edit' => 'inline',
                        'inline' => 'table',
                        'sortable' => 'position',
                        'link_parameters' => array('provider' => 'sonata.media.provider.image'),
                        'context' => 'default',
                        'admin_code' => 'sonata.media.admin.media',
                    ))
                ->end()
            ->end()    
        ;
         $formMapper    
            ->tab('Image French')
                ->with('French')   
                    ->add('FrenchDesktopAdd', 'sonata_type_model_list', array('label' => 'Desktop Image'), array(
                        'edit' => 'inline',
                        'inline' => 'table',
                        'sortable' => 'position',
                        'link_parameters' => array('provider' => 'sonata.media.provider.image'),
                        'context' => 'default',
                        'admin_code' => 'sonata.media.admin.media',
                    ))
                    ->add('FrenchMobileAdd', 'sonata_type_model_list', array('label' => 'Mobile Image'), array(
                        'edit' => 'inline',
                        'inline' => 'table',
                        'sortable' => 'position',
                        'link_parameters' => array('provider' => 'sonata.media.provider.image'),
                        'context' => 'default',
                        'admin_code' => 'sonata.media.admin.media',
                    ))
                    ->add('FrenchTabletAdd', 'sonata_type_model_list', array('label' => 'Tablet Image'), array(
                        'edit' => 'inline',
                        'inline' => 'table',
                        'sortable' => 'position',
                        'link_parameters' => array('provider' => 'sonata.media.provider.image'),
                        'context' => 'default',
                        'admin_code' => 'sonata.media.admin.media',
                    ))
                ->end()
            ->end()    
        ;
        $formMapper    
            ->tab('Client Information')
                ->with('For administration')
                    ->add('ClientName', 'text', array('label' => 'Client Name', 'required' => false))
                    ->add('price','number', array('required' => false))
                    ->add('remarks', 'text', array('label' => 'Remarks', 'required' => false))
                    ->add('DesignBypweb', ChoiceType::class,[
                        'required' => false,
                        'choices' => [
                            'Yes' => 'Yes',
                            'No' => 'No',
                        ],
                        'label' => 'Design by Pweb'
                    ])
                    ->add('ReminderDate', DateType::class, [
                        'required' => false,
                        'label' => 'Send reminder date',
                        'widget' => 'single_text', 
                    ])
                ->end()
            ->end()    
        ;
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $formMapper    
                ->tab('Client Information')
                    ->with('For administration')
                        ->add('invoiced', ChoiceType::class,[
                            'required' => false,
                            'choices' => [
                                'Yes' => 'Yes',
                                'No' => 'No',
                            ],
                        ])
                    ->end()
                ->end()    
            ;     
        }
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('title')
            ->add('userid')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('startdate')
            ->add('enddate')
            ->add('status')
            ->add('desktop_views', null, ['label' => 'Desktop Views'])
            ->add('mobile_views', null, ['label' => 'Mobile Views'])
            ->add('tablet_views', null, ['label' => 'Tablet Views'])
            ->add('desktop_counter', null, ['label' => 'Desktop Clicks'])
            ->add('mobile_counter', null, ['label' => 'Mobile Clicks'])
            ->add('tablet_counter', null, ['label' => 'Tablet Clicks'])
            ->add('EngDesktopAdd', null, ['label' => 'English Desktop Add'])
            ->add('EngMobileAdd', null, ['label' => 'English Mobile Add'])
            ->add('EngTabletAdd', null, ['label' => 'English Tablet Add'])
            ->add('DutchDesktopAdd')
            ->add('DutchMobileAdd')
            ->add('DutchTabletAdd')
            ->add('FrenchDesktopAdd')
            ->add('FrenchMobileAdd')
            ->add('FrenchTabletAdd')
            ->add('link')
            ->add('zone')
            ->add('ClientName', null, ['label' => 'Client Name'])
            ->add('price')
            ->add('remarks')
            ->add('DesignBypweb', null, ['label' => 'Design-By-Pweb'])
            ->add('ReminderDate', null, ['label' => 'Reminder Date'])
        ;
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $showMapper
                ->add('invoiced')    
            ;     
        }
    }
    public function configure() {
        $this->setTemplate('edit', 'GalopAdminBundle:Advertise:advertise.html.twig');
    }
}