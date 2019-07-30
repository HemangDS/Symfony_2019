<?php

namespace iFlair\LetsBonusAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use iFlair\LetsBonusAdminBundle\Entity\VoucherPrograms;
use Symfony\Component\HttpFoundation\Session\Session;

class NewsletterAdmin extends Admin
{
    // protected $baseRoutePattern = 'newsletter';

    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection
            // ->add('loadvoucherprogramsextrafields', 'loadvoucherprogramsextrafields')
            ->add('testCampaign', $this->getRouterIdParameter().'/testCampaign')
            ->add('newsletterdateselection', 'newsletterdateselection')
            ->add('newslettertest', 'newslettertest')
            ->add('preview', $this->getRouterIdParameter().'/preview')
            ->add('campaigncreate', $this->getRouterIdParameter().'/campaigncreate')
            ->add('campaignsend', $this->getRouterIdParameter().'/campaignsend')
        ;
    }

    public function getTemplate($name)
    {
        switch ($name) {
            case 'edit':
                return 'iFlairLetsBonusAdminBundle:Newsletter:base_edit.html.twig';
            break;
            default:
                return parent::getTemplate($name);
            break;
        }
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('nname')
            ->add('templatename')
            ->add('ndate')
            ->add('asunto')
            ->add('bannername')
            ->add('list')
            ->add('shopblocktitle')
            ->add('title')
            ->add('voucherblocktitle')
            ->add('programName')
            ->add('status')
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
                    'preview' => array(
                    'template' => 'iFlairLetsBonusAdminBundle:CRUD:list_action_preview.html.twig',
                        'attr' => array(),
                    ),
                ),
            ))
            ->add('nname')
            ->add('templatename')
            ->add('ndate')
            ->add('asunto')
            ->add('bannername')
            ->add('list')
            ->add('shopblocktitle')
            ->add('title')
            ->add('voucherblocktitle')
            ->add('programName')
            ->add('status')

        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        if ($this->id($this->getSubject())) {
            $da = $this->getRoot()->getSubject()->getNdate();
            $date_selected = $da->format('Y-m-d H:i:s');
            $session = new Session();
            $session->set('SELECTED_DATE', $date_selected);
        }

        $formMapper
            ->tab('Detalle')
                ->with('group1')
                    ->add('nname', null, array('label' => 'Newsletter Name'))
                    ->add('templatename')

        /* DATE AND TIME :: CODE :: COMBINED */
                    ->add('ndate', 'sonata_type_datetime_picker', array(
                            'dp_side_by_side' => true,
                            'dp_use_current' => false,
                            'dp_use_seconds' => false,
                            'datepicker_use_button' => false,
                            //'format' => 'MMM d, yyyy h:m:s a',
                            'format' => 'dd/MM/yyyy HH:mm',
                            'attr' => array('class' => 'newslettervoucher'),
                    ))
        /* DATE AND TIME :: CODE :: COMBINED */

                    ->add('asunto')
                    //->add('listId')
                      ->add('list', 'entity', array(
                            'class' => 'iFlair\LetsBonusAdminBundle\Entity\MailchimpLists',
                            'property' => 'list_name',
                            'multiple' => false,
                            'label' => 'Mailchimp List Name'
                            //  'expanded' => true,
                        ))

                    ->add('status', 'choice', array('choices' => array('ready' => 'Ready', 'notpublished' => 'Not Published', 'ko' => 'KO - ERROR')))
                    ->end()
            ->end()
            ->tab('Contenido')
                ->with('group2')
                       ->add('bannername', 'sonata_type_model_autocomplete', array(
                            'property' => 'bannername',
                            'label' => 'Banner Name',
                            'class' => 'iFlairLetsBonusAdminBundle:NewsletterBanner',
                            'placeholder' => 'Please Select Banners',
                            'multiple' => true,
                            'by_reference' => true,
                            'attr' => ['style' => 'width: 100%;'],
                            'required' => true,
                            'to_string_callback' => function ($entity) {
                                return $entity->getBannername();
                            },
                        )
                    )

                    ->add('shopblocktitle', null, ['attr' => ['placeholder' => 'Shop Block Title']])

                       ->add('title', 'sonata_type_model_autocomplete', array(
                            'property' => 'title',
                            'label' => 'Cash back (Shop Names)',
                            'class' => 'iFlairLetsBonusAdminBundle:shopHistory',
                            'placeholder' => 'Please Select Shops',
                            'multiple' => true,
                            'by_reference' => true,
                            'attr' => ['style' => 'width: 100%;', 'class' => 'cashbackshopautocomplete'],
                            'required' => true,
                            'callback' => function ($admin, $property, $value) {
                                $session = new Session();
                                $selected_date = $session->get('SELECTED_DATE');
                                //echo "session: ".$selected_date."\n";
                                $newtime = '';
                                if (is_object($selected_date)) {
                                    $newtime = $selected_date;
                                } else {
                                    $selected_date = str_replace('/', '-', $selected_date);
                                    //echo "formatted date: ".date('Y-m-d H:i:s', strtotime($selected_date))."\n";
                                    //echo "formatted without strtotime date: ".date('Y-m-d H:i:s', $selected_date)."\n";
                                    $selected_date = date('Y-m-d H:i:s', strtotime($selected_date));
                                    $newtime = new \DateTime($selected_date);
                                }
                                //$newtime = new \DateTime($selected_date);
                                $datagrid = $admin->getDatagrid();
                                $queryBuilder = $datagrid->getQuery();
                                $queryBuilder
                                    ->andWhere($queryBuilder->getRootAlias().'.startDate <= :dateValue')
                                    //->andWhere($queryBuilder->getRootAlias().'.endDate > :dateValue')
                                    ->setParameter('dateValue', $newtime->format('Y-m-d H:i:s'))
                                ;
                                //echo get_class($queryBuilder);
                                /*echo $queryBuilder->getQuery()->getSQL();
                                $query = $queryBuilder->getQuery();
                                print_r(array(
                                    'sql'        => $query->getSQL(),
                                    'parameters' => $query->getParameters(),
                                ));*/
                                //echo "<pre>";print_r($newtime);exit;
                                $datagrid->setValue($property, null, $value);
                            },
                        )
                    )

                    ->add('voucherblocktitle', null, ['attr' => ['placeholder' => 'Voucher Block Title']])

                    ->add('programName', 'sonata_type_model_autocomplete', array(
                            'property' => 'programName',
                            'placeholder' => 'Please Select Brands',
                            'label' => 'Brand Names',
                            'class' => 'iFlairLetsBonusAdminBundle:VoucherPrograms',
                            'multiple' => true,
                            'by_reference' => true,
                            'attr' => ['style' => 'width: 100%;'],
                            'required' => true,
                            'to_string_callback' => function ($entity, $property) {
                                return $entity->getProgramName();
                            },
                        )
                    )
                ->end()
            ->end()
            ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('nname')
            ->add('templatename')
            ->add('ndate')
            ->add('asunto')
            ->add('bannername')
            ->add('list')
            ->add('shopblocktitle')
            ->add('title')
            ->add('voucherblocktitle')
            ->add('programName')
            ->add('status')
        ;
    }

    public function preUpdate($object)
    {
       $object->setModified(new \DateTime());
    }
}
