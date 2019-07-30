<?php

namespace iFlair\LetsBonusAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use iFlair\LetsBonusAdminBundle\Entity\Shop;
use Sonata\AdminBundle\Route\RouteCollection;

class ShopAdmin extends Admin
{
    /*protected $baseRoutePattern = 'ShopAdmin';

    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection
            ->add('loadVouchers', 'loadVouchers')
        ;
    }*/
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('delete');
        parent::configureRoutes($collection);
        $collection->add('loadVouchers', 'loadVouchers');
        $collection->add('loadShopHistory', 'loadShopHistory');
    }

    public function getTemplate($name)
    {
        switch ($name) {
            case 'edit':
                return 'iFlairLetsBonusAdminBundle:Shop:voucher.html.twig';
                break;
            default:
                return parent::getTemplate($name);
                break;
        }
    }

    protected $shopStatusList = array(
            Shop::SHOP_DEACTIVATED => 'Deactivated',
            Shop::SHOP_ACTIVATED => 'Activated',
            Shop::SHOP_EDITORIAL => 'Editorial',
            Shop::SHOP_MARKETING => 'Marketing',
        );

    /**
     * @param DatagridMapper $datagridMapper
     */
    /*protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('companies')
            ->add('keywords')
            ->add('network')
            ->add('vprogram')
            ->add('urlAffiliate')
            ->add('startDate')
            ->add('endDate')
            ->add('categories')
            ->add('collections')
        ;
    }*/

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
                    //'delete' => array(),
                ),
            ))
            ->add('id')
            ->add('image', null, array(
                'template' => 'iFlairLetsBonusAdminBundle:Shop:picture.html.twig',
            ))
            ->add('title')
            ->add('vprogram')
            ->add('offers')
            ->add('shopStatus', 'choice', array('choices' => $this->shopStatusList))
            ->add('highlightedHome', 'choice', array('choices' => array('0' => 'No', '1' => 'Yes')))
            ->add('highlightedOffer', 'choice', array('label' => 'Oferta del día', 'choices' => array('0' => 'No', '1' => 'Yes')))
            ->add('urlAffiliate')
            ->add('cashbackPrice')
            ->add('cashbackPercentage')
            ->add('letsBonusPercentage')
            ->add('startDate')
            ->add('endDate')
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $flagMediaImage = 1;
        $flagMediaLogo = 1;
        $flagMediaLogoV2 = 1;
        $flagMediaImageV2 = 1;
        if (!$this->getRequest()->get($this->getIdParameter())) {
            $formMapper
                ->tab('General')
                    ->with('General')
                        ->add('title','text',array('required' => true)) 
                        ->add('url','text',array('label' => 'URL Text'))
                        ->add('introduction', 'textarea', array('attr' => array('class' => 'ckeditor')))
                        ->add('description', 'textarea', array('attr' => array('class' => 'ckeditor')))
                        ->add('tearms', 'textarea', array('attr' => array('class' => 'ckeditor')))
                        ->add('administrator', 'entity', array(
                            'class' => 'Application\Sonata\UserBundle\Entity\User',
                            'property' => 'username',
                        ))
                        ->add('companies', 'entity', array(
                            'class' => 'iFlair\LetsBonusAdminBundle\Entity\Companies',
                            'property' => 'name',
                        ))
                        ->add('keywords')
                        ->add('cashbackPrice', null, array('required' => false))
                        ->add('cashbackPercentage', null, array('required' => false))
                        ->add('programId', null, array('required' => true))
                        ->add('urlAffiliate')
                        ->add('daysValidateConfirmation')
                        ->add('highlightedHome', 'choice', array('choices' => array('0' => 'No', '1' => 'Yes')))
                        ->add('highlightedOffer', 'choice', array('label' => 'Oferta del día', 'choices' => array('0' => 'No', '1' => 'Yes')))
                        ->add('shopStatus', 'choice', array('choices' => array('2' => 'Marketing', '3' => 'Editorial', '1' => 'Activated', '0' => 'Deactivated')))
                        ->add('startDate', 'sonata_type_datetime_picker', array('label' => 'Start date', 'dp_language' => 'de', 'format' => 'yyyy-MM-dd hh:mm:ss', 'read_only' => true, 'attr' => array('data-date-format' => 'yyyy-MM-dd hh:mm:ss')))
                        ->add('endDate',   'sonata_type_datetime_picker', array('label' => 'End date', 'dp_language' => 'en', 'format' => 'yyyy-MM-dd hh:mm:ss', 'read_only' => true))
                    ->end()
                ->end()
                ->tab('Image')
                    ->with('Image')
                        ->add('image', 'sonata_media_type', array(
                            'provider' => 'sonata.media.provider.image',
                            'context' => 'default',
                        ))
                        ->add('tabImage', 'sonata_media_type', array(
                            'provider' => 'sonata.media.provider.image',
                            'context' => 'default',
                        ))
                        ->add('categoryImage', 'sonata_media_type', array(
                            'provider' => 'sonata.media.provider.image',
                            'context' => 'default',
                        ))
                        ->add('newsletterImage', 'sonata_media_type', array(
                            'provider' => 'sonata.media.provider.image',
                            'context' => 'default',
                        ))
                        ->add('highlineofferImage', 'sonata_media_type', array(
                            'provider' => 'sonata.media.provider.image',
                            'context' => 'default',
                        ))
                    ->end()
                ->end()
                ->tab('Categories')
                    ->with('group4')
                        ->add('parentCategory', 'entity', array(
                            'class' => 'iFlair\LetsBonusAdminBundle\Entity\parentCategory',
                            'property' => 'name',
                            'multiple' => true,
                            'expanded' => true,
                        ))
                        ->add('categories', 'entity', array(
                            'class' => 'iFlair\LetsBonusAdminBundle\Entity\Category',
                            'property' => 'name',
                            'multiple' => true,
                            'expanded' => true,
                        ))
                        ->add('childCategory', 'entity', array(
                            'class' => 'iFlair\LetsBonusAdminBundle\Entity\childCategory',
                            'property' => 'name',
                            'multiple' => true,
                            'expanded' => true,
                        ))
                    ->end()
                ->end()
                ->tab('LetsBonus(%)')
                    ->with('LetsBonus(%)')
                        ->add('letsBonusPercentage')
                    ->end()
                ->end()
                ->tab('Variation')
                    ->with('group6')
                        ->add('shopVariation', 'sonata_type_collection', array(
                            'cascade_validation' => true,
                            'by_reference' => false,
                            'label' => 'Variation\'s',
                        ), array(
                                'edit' => 'inline',
                                'inline' => 'table',
                                'sortable' => 'position',
                                'link_parameters' => array('context' => 'default'),
                                'admin_code' => 'i_flair_lets_bonus_admin.admin.shop_variation',
                            )
                        )
                    ->end()
                ->end()
                ->tab('Labels')
                    ->with('Labels')
                        ->add('tag', 'entity', array(
                            'class' => 'iFlair\LetsBonusAdminBundle\Entity\Tags',
                            'property' => 'name',
                            'required' => false,
                        ))
                        ->add('prevLabelCrossedOut')
                        ->add('shippingInfo')
                    ->end()
                ->end()
                ->tab('Collections')
                    ->with('group8')
                        ->add('collections', 'entity', array(
                            'class' => 'iFlair\LetsBonusAdminBundle\Entity\Collection',
                            'property' => 'name',
                            'multiple' => true,
                            'expanded' => true,
                            'required' => false,
                        ))
                    ->end()
                ->end()
                ->tab('Internal notes')
                    ->with('group9')
                        ->add('internalNotes', 'textarea', array('required' => false))
                    ->end()
                ->end()
                ->tab('Offers and vouchers')
                    ->with('Offers and vouchers')
                        ->add('exclusive', null, array('required' => false, 'label' => 'Is Exclusive Shop?'))
                        ->add('offers', 'choice', array('choices' => array('' => 'Select offers', 'cashback' => 'Cashback', 'voucher' => 'Vouchers (coupons)', 'offer' => 'Products','cashback/coupons' => 'Cashback/Coupons')))
                        ->add('network', 'entity', array(
                            'class' => 'iFlair\LetsBonusAdminBundle\Entity\Network',
                            'property' => 'name',
                            'multiple' => false,
                            'expanded' => false,
                        ))
                        ->add('networkCredentials', 'shtumi_dependent_filtered_entity', array(
                            'entity_alias' => 'credentials_by_network',
                            'parent_field' => 'network',
                            'required' => true,
                        ))
                        ->add('vprogram', 'shtumi_dependent_filtered_entity', array(
                            'entity_alias' => 'program_by_network',
                            'parent_field' => 'network',
                            'required' => true,
                            'attr' => array('class' => 'shop_voucher_program'),
                        ))
                       /* ->add('voucher', 'shtumi_dependent_filtered_entity', array(
                            'entity_alias' => 'voucher_by_program',
                            'parent_field' => 'vprogram',
                            'required' => false,
                        ))*/
                        ->add('voucher', 'entity', array(
                            'class' => 'iFlairLetsBonusAdminBundle:Voucher',
                            'property' => 'title',
                            'required' => false,
                            'multiple' => true,
                            'attr' => array('class' => 'shop_voucher'),
                        ))
                        /*->add('voucher', 'entity', array(
                            'class' => 'iFlair\LetsBonusAdminBundle\Entity\Voucher',
                            'property' => 'title',
                            'multiple' => true,
                            'expanded' => true,
                        ))*/
                    ->end()
                ->end();
        } else {
            $formMapper
                ->tab('General')
                    ->with('group1')
                        ->add('url','text',array('label' => 'URL Text'))
                       /*  ->add('title','text') 
                        ->add('introduction', 'textarea', array('attr' => array('class' => 'ckeditor')))
                        ->add('description', 'textarea', array('attr' => array('class' => 'ckeditor')))
                        ->add('tearms', 'textarea', array('attr' => array('class' => 'ckeditor')))
                       */
                         ->add('administrator', 'entity', array(
                            'class' => 'Application\Sonata\UserBundle\Entity\User',
                            'property' => 'username',
                        ))
                        ->add('companies', 'entity', array(
                            'class' => 'iFlair\LetsBonusAdminBundle\Entity\Companies',
                            'property' => 'name',
                        ))
                        ->add('keywords')
                        ->add('cashbackPrice')
                        ->add('cashbackPercentage')
                        ->add('programId', null, array('required' => true))
                        ->add('urlAffiliate')
                        ->add('daysValidateConfirmation')
                        ->add('highlightedHome', 'choice', array('choices' => array('0' => 'No', '1' => 'Yes')))
                        ->add('highlightedOffer', 'choice', array('label' => 'Oferta del día', 'choices' => array('0' => 'No', '1' => 'Yes')))
                        ->add('shopStatus', 'choice', array('choices' => array('2' => 'Marketing', '3' => 'Editorial', '1' => 'Activated', '0' => 'Deactivated')))
                        ->add('startDate', 'sonata_type_datetime_picker', array('label' => 'Start date', 'dp_language' => 'de', 'format' => 'yyyy-MM-dd hh:mm:ss', 'read_only' => true, 'attr' => array('data-date-format' => 'yyyy-MM-dd hh:mm:ss')))
                        ->add('endDate', 'sonata_type_datetime_picker', array('label' => 'End date', 'dp_language' => 'en', 'format' => 'yyyy-MM-dd hh:mm:ss', 'read_only' => true))
                    ->end()
                ->end();

            $image = $this->getSubject();

            if ($image) {
                $container = $this->getConfigurationPool()->getContainer();
                $em = $container->get('Doctrine');

                $settingsRepository = $em->getRepository('iFlairLetsBonusAdminBundle:Settings');

                $mediaImage = $image->getImage();
                if (!empty($mediaImage)) {
                    if (count($mediaImage->getProviderMetadata()) > 0 && !empty($mediaImage->getProviderMetadata())) {
                        $flagMediaImage = 1;
                        $shopMediaImagePreview = $settingsRepository->getMediaPreviewOverEditMode($mediaImage, $container);
                    } else {
                        $flagMediaImage = 0;
                    }
                } else {
                    $flagMediaImage = 0;
                }
                $mediaLogo = $image->getTabImage();
                if (!empty($mediaLogo)) {
                    if (count($mediaLogo->getProviderMetadata()) > 0 && !empty($mediaLogo->getProviderMetadata())) {
                        $flagMediaLogo = 1;
                        $shopMediaLogoPreview = $settingsRepository->getMediaPreviewOverEditMode($mediaLogo, $container);
                    } else {
                        $flagMediaLogo = 0;
                    }
                } else {
                    $flagMediaLogo = 0;
                }
                $mediaLogoV2 = $image->getCategoryImage();
                if (!empty($mediaLogoV2)) {
                    if (count($mediaLogoV2->getProviderMetadata()) > 0 && !empty($mediaLogoV2->getProviderMetadata())) {
                        $flagMediaLogoV2 = 1;
                        $shopMediaLogoV2Preview = $settingsRepository->getMediaPreviewOverEditMode($mediaLogoV2, $container);
                    } else {
                        $flagMediaLogoV2 = 0;
                    }
                } else {
                    $flagMediaLogoV2 = 0;
                }
                $mediaImageV2 = $image->getNewsletterImage();
                if (!empty($mediaImageV2)) {
                    if (count($mediaImageV2->getProviderMetadata()) > 0 && !empty($mediaImageV2->getProviderMetadata())) {
                        $flagMediaImageV2 = 1;
                        $shopMediaImageV2Preview = $settingsRepository->getMediaPreviewOverEditMode($mediaImageV2, $container);
                    } else {
                        $flagMediaImageV2 = 0;
                    }
                } else {
                    $flagMediaImageV2 = 0;
                }

                $mediaLogoV3 = $image->getHighlineofferImage();
                if (!empty($mediaLogoV3)) {
                    if (count($mediaLogoV3->getProviderMetadata()) > 0 && !empty($mediaLogoV3->getProviderMetadata())) {
                        $flagMediaLogoV3 = 1;
                        $shopMediaLogoV3Preview = $settingsRepository->getMediaPreviewOverEditMode($mediaLogoV3, $container);
                    } else {
                        $flagMediaLogoV3 = 0;
                    }
                } else {
                    $flagMediaLogoV3 = 0;
                }
            }

            if ($flagMediaImage) {
                $formMapper
                    ->tab('Image')
                        ->with('group2');
                $formMapper->add('image', 'sonata_media_type', array(
                                'provider' => 'sonata.media.provider.image',
                                'context' => 'default',
                                'required' => false,
                                'help' => $shopMediaImagePreview,
                            ))
                        ->end()
                    ->end();
            } else {
                $formMapper
                    ->tab('Image')
                        ->with('group2');
                $formMapper->add('image', 'sonata_media_type', array(
                                'provider' => 'sonata.media.provider.image',
                                'context' => 'default',
                                'required' => false,
                            ))
                        ->end()
                    ->end();
            }

            if ($flagMediaLogo) {
                $formMapper
                    ->tab('Image')
                        ->with('group2');
                $formMapper->add('tabImage', 'sonata_media_type', array(
                                'provider' => 'sonata.media.provider.image',
                                'context' => 'default',
                                'required' => false,
                                'help' => $shopMediaLogoPreview,
                            ))
                        ->end()
                    ->end();
            } else {
                $formMapper
                    ->tab('Image')
                        ->with('group2');
                $formMapper->add('tabImage', 'sonata_media_type', array(
                                'provider' => 'sonata.media.provider.image',
                                'context' => 'default',
                                'required' => false,
                            ))
                        ->end()
                    ->end();
            }

            if ($flagMediaLogoV2) {
                $formMapper
                    ->tab('Image')
                        ->with('group2');
                $formMapper->add('categoryImage', 'sonata_media_type', array(
                                'provider' => 'sonata.media.provider.image',
                                'context' => 'default',
                                'required' => false,
                                'help' => $shopMediaLogoV2Preview,
                            ))
                        ->end()
                    ->end();
            } else {
                $formMapper
                    ->tab('Image')
                        ->with('group2');
                $formMapper->add('categoryImage', 'sonata_media_type', array(
                                'provider' => 'sonata.media.provider.image',
                                'context' => 'default',
                                'required' => false,
                            ))
                        ->end()
                    ->end();
            }

            if ($flagMediaImageV2) {
                $formMapper
                    ->tab('Image')
                        ->with('group2');
                $formMapper->add('newsletterImage', 'sonata_media_type', array(
                                'provider' => 'sonata.media.provider.image',
                                'context' => 'default',
                                'required' => false,
                                'help' => $shopMediaImageV2Preview,
                            ))
                        ->end()
                    ->end();
            } else {
                $formMapper
                    ->tab('Image')
                        ->with('group2');
                $formMapper->add('newsletterImage', 'sonata_media_type', array(
                                'provider' => 'sonata.media.provider.image',
                                'context' => 'default',
                                'required' => false,
                            ))
                        ->end()
                    ->end();
            }

             if ($flagMediaLogoV3) {
                $formMapper
                    ->tab('Image')
                        ->with('group2');
                $formMapper->add('highlineofferImage', 'sonata_media_type', array(
                                'provider' => 'sonata.media.provider.image',
                                'context' => 'default',
                                'required' => false,
                                'help' => $shopMediaLogoV3Preview,
                            ))
                        ->end()
                    ->end();
            } else {
                $formMapper
                    ->tab('Image')
                        ->with('group2');
                $formMapper->add('highlineofferImage', 'sonata_media_type', array(
                                'provider' => 'sonata.media.provider.image',
                                'context' => 'default',
                                'required' => false,
                            ))
                        ->end()
                    ->end();
            }
            
            $formMapper
                ->tab('Categories')
                    ->with('group4')
                        ->add('parentCategory', 'entity', array(
                            'class' => 'iFlair\LetsBonusAdminBundle\Entity\parentCategory',
                            'property' => 'name',
                            'multiple' => true,
                            'expanded' => true,
                        ))
                        ->add('categories', 'entity', array(
                            'class' => 'iFlair\LetsBonusAdminBundle\Entity\Category',
                            'property' => 'name',
                            'multiple' => true,
                            'expanded' => true,
                        ))
                        ->add('childCategory', 'entity', array(
                            'class' => 'iFlair\LetsBonusAdminBundle\Entity\childCategory',
                            'property' => 'name',
                            'multiple' => true,
                            'expanded' => true,
                        ))
                    ->end()
                ->end()
               /* ->tab('LetsBonus(%)')
                    ->with('LetsBonus(%)')
                        ->add('letsBonusPercentage')
                        //->add('letsBonusPercentage','string',array(),array('template' => 'iFlairLetsBonusAdminBundle:ShopHistory:list_custom.html.twig'))
                    ->end()
                ->end()
                ->tab('Variation')
                    ->with('group6')
                        ->add('shopVariation', 'sonata_type_collection', array(
                            'cascade_validation' => true,
                            'by_reference' => false,
                            'label' => 'Variation\'s',
                        ), array(
                                'edit' => 'inline',
                                'inline' => 'table',
                                'sortable' => 'position',
                                'link_parameters' => array('context' => 'default'),
                                'admin_code' => 'i_flair_lets_bonus_admin.admin.shop_variation',
                            )
                        )
                    ->end()
                ->end()
                ->tab('Labels')
                    ->with('Labels')
                        ->add('tag', 'entity', array(
                            'class' => 'iFlair\LetsBonusAdminBundle\Entity\Tags',
                            'property' => 'name',
                            'required' => false,
                        ))
                        ->add('prevLabelCrossedOut')
                        ->add('shippingInfo')
                    ->end()
                ->end()*/
                ->tab('History')
                    ->with('History')
                        //->add('url')
                        /*->add('shopHistory', 'sonata_type_admin', array(), array(
                            'admin_code' => 'i_flair_lets_bonus_admin.admin.shop_history'
                        ))*/
                       // ->add('shopHistory', 'sonata_type_model', array('expanded' => true, 'by_reference' => false, 'multiple' => true))
                        /*->add('shopHistory', 'sonata_type_model_hidden', array(), array(
                            'admin_code' => 'i_flair_lets_bonus_admin.admin.shop_history'
                        ))*/
                        /*->add('shopHistory', 'hidden', array(), array(
                            'admin_code' => 'i_flair_lets_bonus_admin.admin.shop_history'
                        ))*/
                    ->end()
                ->end()
                ->tab('Collections')
                    ->with('group8')
                        ->add('collections', 'entity', array(
                            'class' => 'iFlair\LetsBonusAdminBundle\Entity\Collection',
                            'property' => 'name',
                            'multiple' => true,
                            'expanded' => true,
                            'required' => false,
                        ))
                    ->end()
                ->end()
                ->tab('Internal notes')
                    ->with('group9')
                        ->add('internalNotes', 'textarea', array('required' => false))
                    ->end()
                ->end()
                ->tab('Offers and vouchers')
                    ->with('Offers and vouchers')
                        ->add('exclusive', null, array('required' => false, 'label' => 'Is Exclusive Shop?'))
                        ->add('offers', 'choice', array('choices' => array('' => 'Select offers', 'cashback' => 'Cashback', 'voucher' => 'Vouchers (coupons)', 'offer' => 'Products','cashback/coupons' => 'Cashback/Coupons')))
                        ->add('network', 'entity', array(
                            'class' => 'iFlair\LetsBonusAdminBundle\Entity\Network',
                            'property' => 'name',
                            'multiple' => false,
                            'expanded' => false,
                        ))
                        ->add('networkCredentials', 'shtumi_dependent_filtered_entity', array(
                            'entity_alias' => 'credentials_by_network',
                            'parent_field' => 'network',
                            'required' => true,
                        ))
                        ->add('vprogram', 'shtumi_dependent_filtered_entity', array(
                            'entity_alias' => 'program_by_network',
                            'parent_field' => 'network',
                            'required' => true,
                            'attr' => array('class' => 'shop_voucher_program'),
                        ))
                        /*->add('voucher', 'entity', array(
                            'class' => 'iFlair\LetsBonusAdminBundle\Entity\Voucher',
                            'property' => 'title',
                            'multiple' => true,
                            'expanded' => true,
                        ))*/
                        ->add('voucher', 'entity', array(
                            'class' => 'iFlairLetsBonusAdminBundle:Voucher',
                            'property' => 'title',
                            'required' => false,
                            'multiple' => true,
                            'attr' => array('class' => 'shop_voucher'),
                        ))
                    ->end()
                ->end();
        }
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('companies')
            ->add('keywords')
            ->add('network')
            ->add('vprogram')
            ->add('urlAffiliate')
            ->add('daysValidateConfirmation')
            ->add('highlightedHome')
            ->add('highlightedOffer')
            ->add('shopStatus', 'choice', array('choices' => $this->shopStatusList))
            ->add('startDate')
            ->add('endDate')
            ->add('tabImage')
            ->add('image')
            ->add('categoryImage')
            ->add('newsletterImage')
            ->add('categories')
            ->add('collections')
            ->add('internalNotes');
    }

    public function postPersist($object)
    {
        //$entity = $object->getEntity();
        //$object->getId();
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->addRowShopHistory($em, $object);
    }

    public function prePersist($object)
    {
        $uniqid = $this->getRequest()->query->get('uniqid');
        $formData = $this->getRequest()->request->get($uniqid);
        $object->setAdministrator($formData['administrator']);
        //$em = $this->getModelManager()->getEntityManager($this->getClass());
        //$em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->addDataShopHistory($em, $formData, $object);
    }

    public function preUpdate($object)
    {
        $uniqid = $this->getRequest()->query->get('uniqid');
        $formData = $this->getRequest()->request->get($uniqid);
        $object->setAdministrator($formData['administrator']);
        //$em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->addDataShopHistory($em, $formData, $object);
    }

    public function postUpdate($object)
    {
        //$entity = $object->getEntity();
        //$object->getId();
        /*echo '<pre>';
        print_r($object->getVariation());exit;*/
        $uniqid = $this->getRequest()->query->get('uniqid');
        $formData = $this->getRequest()->request->get($uniqid);
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->updateRowShopHistory($em, $object, $formData);
    }

    public function preRemove($object)
    {
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        /*$shopHistory = $em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array('shop' => $object->getId()));
        $variations = $em->getRepository('iFlairLetsBonusAdminBundle:Variation')->findAll(array('shop_history_id' => $shopHistory->getId()));
        foreach($variations as $variation){
            echo $variation->getId();
        }
        echo $shopHistory->getId();
        echo $object->getId();exit;*/

        $em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->removeRowShopHistory($em, $object);
    }
}
