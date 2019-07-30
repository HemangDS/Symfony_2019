<?php

namespace iFlair\LetsBonusAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
 use iFlair\LetsBonusAdminBundle\Entity\Variation;

class shopHistoryAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('shop')
            ->add('title')
            ->add('url')
            ->add('cashbackPrice')
            ->add('cashbackPercentage')
            ->add('letsBonusPercentage')
            ->add('startDate')
            ->add('tag')
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
                    'Clone' => array(
                        'template' => 'iFlairLetsBonusAdminBundle:ShopHistory:list_action_clone.html.twig',
                    ),
                ),
            ))
            ->add('id')
            ->add('title')
            ->add('cashbackPrice')
            ->add('cashbackPercentage')
            ->add('letsBonusPercentage')
            ->add('show_on_como_functiona', 'choice', array('choices' => array('0' => 'No', '1' => 'Yes')))
            ->add('startDate', 'datetime', array('label' => 'Start date', 'pattern' => 'yyyy-mm-dd hh:mm:ss', 'locale' => 'en', 'timezone' => 'Europe/Paris'));
    }

    protected function configureRoutes(\Sonata\AdminBundle\Route\RouteCollection $collection)
    {
        $collection->add('clone', $this->getRouterIdParameter().'/clone');
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {

        $formMapper
            ->tab('General')
                ->with('General')
                    ->add('shop', 'sonata_type_model_hidden', array(
                      ))
                    ->add('administrator', 'entity', array(
                        'class' => 'Application\Sonata\UserBundle\Entity\User',
                        'property' => 'username',
                    ))
                    ->add('title','text',array('required' => true)) 
                    ->add('url','text',array('label' => 'URL Text'))
                    ->add('introduction', 'textarea', array('attr' => array('class' => 'ckeditor')))
                    ->add('description', 'textarea', array('attr' => array('class' => 'ckeditor')))
                    ->add('tearms', 'textarea', array('attr' => array('class' => 'ckeditor')))
                    ->add('cashbackPrice', null, array('required' => false))
                    ->add('cashbackPercentage', null, array('required' => false))
                    ->add('urlAffiliate')
                    ->add('show_on_como_functiona', 'choice', array('choices' => array('0' => 'No', '1' => 'Yes')))
                    ->add('startDate', 'sonata_type_datetime_picker', array('label' => 'Start date', 'dp_language' => 'de', 'format' => 'yyyy-MM-dd hh:mm:ss', 'read_only' => true, 'attr' => array('data-date-format' => 'yyyy-MM-dd hh:mm:ss')))
                ->end()
            ->end()
            ->tab('LetsBonus(%)')
                ->with('LetsBonus(%)')
                    ->add('letsBonusPercentage')
                ->end()
            ->end()
            ->tab('Variation')
                ->with('group6')
                    ->add('variation', 'sonata_type_collection', array(
                        'cascade_validation' => true,
                        'by_reference' => false,
                        'label' => 'Variation\'s',
                    ), array(
                            'edit' => 'inline',
                            'inline' => 'table',
                            'sortable' => 'position',
                            'link_parameters' => array('context' => 'default'),
                            'admin_code' => 'i_flair_lets_bonus_admin.admin.variation',
                        )
                    )
                ->end()
            ->end()
            ->tab('Labels')
                ->with('Labels')
                    ->add('tag', 'entity', array(
                        'required' => false,
                        'placeholder' => 'Choose label',
                        'class' => 'iFlair\LetsBonusAdminBundle\Entity\Tags',
                        'property' => 'name',
                    ))
                    ->add('prevLabelCrossedOut')
                    ->add('shippingInfo')
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
            ->add('id')
            ->add('shop')
            ->add('administrator')
            ->add('title')
            ->add('url')
            ->add('introduction')
            ->add('description')
            ->add('tearms')
            ->add('cashbackPrice')
            ->add('cashbackPercentage')
            ->add('letsBonusPercentage')
            ->add('show_on_como_functiona')
            ->add('urlAffiliate')
            ->add('startDate', 'datetime', array('label' => 'Start date', 'pattern' => 'yyyy-mm-dd hh:mm:ss', 'locale' => 'en', 'timezone' => 'Europe/Paris'))
            ->add('tag')
            ->add('prevLabelCrossedOut')
            ->add('shippingInfo')
        ;
    }

    public function preUpdate($object)
    {
        $uniqid = $this->getRequest()->query->get('uniqid');
        $formData = $this->getRequest()->request->get($uniqid);
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->hasToUpdateCashBackAmount($em, $formData, $object);
        $object->setVariation($object->getVariation());
    }

    public function prePersist($object)
    {
        $uniqid = $this->getRequest()->query->get('uniqid');
        $formData = $this->getRequest()->request->get($uniqid);
        //Add date time if not added while creation of shop history
        if(empty($formData['startDate'])){
            $object->setStartDate(new \DateTime(date("Y-m-d H:i:s")));
        }
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->hasToUpdateCashBackAmount($em, $formData, $object);
        $object->setVariation($object->getVariation());
    }
}
