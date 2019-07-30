<?php

namespace iFlair\LetsBonusAdminBundle\Admin;

use iFlair\LetsBonusAdminBundle\Entity\Network;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class networkCredentialsAdmin extends Admin
{
    //protected $baseRoutePattern = 'networkcredentials';

    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection
            ->add('loadcredentialsfields', 'loadcredentialsfields')
        ;
    }

    public function getTemplate($name)
    {
        switch ($name) {
            case 'list':
                return 'iFlairLetsBonusAdminBundle:Networks:base_list.html.twig';
                break;
            case 'edit':
                return 'iFlairLetsBonusAdminBundle:Networks:base_edit.html.twig';
                break;
            default:
                return parent::getTemplate($name);
                break;
        }
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    /*protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('network')
            ->add('created')
            ->add('modified')
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
                    'delete' => array(),
                ),
            ))
            ->add('id')
            ->add('network.name')
            ->add('title')
            //->add('amazonUsername', 'string', array('template' => 'iFlairLetsBonusAdminBundle:Networks:amazon.html.twig'))
           // ->add('amazonTitle')
            ->add('amazonUsername')
            ->add('amazonPassword')
            //->add('cjTitle')
            ->add('cjkey')
            ->add('cjurl')
            //->add('ebayTitle')
            ->add('ebayUsername')
            ->add('ebayPassword')
           // ->add('tdtiTitle')
            ->add('tdtiAffiliateId')
            ->add('tdtiKey')
            ->add('tdtiUrl')
           // ->add('tradedoublerTitle')
            ->add('tradedoublerAffiliateId')
            ->add('tradedoublerKey')
            ->add('tradedoublerUrl')
            //->add('webgainsTitle')
            ->add('webgainsUsername')
            ->add('webgainsPassword')
            ->add('webgainsCampaignId')
            ->add('webgainsLocation')
            ->add('webgainsUri')
           // ->add('zenoxTitle')
            ->add('zenoxConnectId')
            ->add('zenoxSecretKey')
            ->add('zenoxRegion')
            /*->add('created')
            ->add('modified')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ))*/
        ;
    }

    /*public function createQuery($context = 'list')
    {
        echo '<pre>';
        print_r($this->getSubject());exit;
        $cName = get_class($this->getSubject());
        $query = $this->getModelManager()->createQuery($cName);

        foreach ($this->extensions as $extension) {
            $extension->configureQuery($this, $query, $context);
        }

        return $query;
    }*/

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        //$session = $this->getRequest()->getSession();

        $subject = $this->getSubject();

        $formMapper
            ->add('network', 'entity', array(
                'class' => 'iFlair\LetsBonusAdminBundle\Entity\Network',
                'property' => 'name',
                'attr' => array('class' => 'selected_network'),
            ))
            ->add('title', null, array(
                'required' => true,
                'label' => 'Title',
            ))
          /*  ->add('amazonTitle', null, array(
                'required' => true,
                'label' => 'Amazon Title',
                'attr' => array('class' => 'network_amazon_title'),
            ))*/
            ->add('amazonUsername', null, array(
                'required' => true,
                'label' => 'Amazon Username',
                'attr' => array('class' => 'network_amazon_username'),
            ))
            ->add('amazonPassword', null, array(
                'required' => true,
                'label' => 'Amazon Password',
                'attr' => array('class' => 'network_amazon_password'),
            ))
          /*  ->add('cjTitle', null, array(
                'required' => true,
                'label' => 'CJ Title',
                'attr' => array('class' => 'network_cj_title'),
            ))*/
            ->add('cjKey', 'textarea', array(
                'required' => true,
                'label' => 'CJ Key',
                'attr' => array('class' => 'network_cj_key'),
            ))
            ->add('cjUrl', null, array(
                'required' => true,
                'label' => 'CJ Url',
                'attr' => array('class' => 'network_cj_url'),
            ))
          /*  ->add('ebayTitle', null, array(
                'required' => true,
                'label' => 'Ebay Title',
                'attr' => array('class' => 'network_ebay_title'),
            ))*/
            ->add('ebayUsername', null, array(
                'required' => true,
                'label' => 'Ebay Username',
                'attr' => array('class' => 'network_ebay_username'),
            ))
            ->add('ebayPassword', null, array(
                'required' => true,
                'label' => 'Ebay Password',
                'attr' => array('class' => 'network_ebay_password'),
            ))
          /*  ->add('tdtiTitle', null, array(
                'required' => true,
                'label' => 'TDTI Title',
                'attr' => array('class' => 'network_tdti_title'),
            ))*/
            ->add('tdtiAffiliateId', null, array(
                'required' => true,
                'label' => 'TDTI Affiliate Id',
                'attr' => array('class' => 'network_tdti_affiliate_id'),
            ))
            ->add('tdtiKey', null, array(
                'required' => true,
                'label' => 'TDTI key',
                'attr' => array('class' => 'network_tdti_key'),
            ))
            ->add('tdtiUrl', null, array(
                'required' => true,
                'label' => 'TDTI URL',
                'attr' => array('class' => 'network_tdti_url'),
            ))
           /* ->add('tradedoublerTitle', null, array(
                'required' => true,
                'label' => 'Tradedoubler Title',
                'attr' => array('class' => 'network_tradedoubler_title'),
            ))*/
            ->add('tradedoublerAffiliateId', null, array(
                'required' => true,
                'label' => 'Tradedoubler Affiliate Id',
                'attr' => array('class' => 'network_tradedoubler_affiliate_id'),
            ))
            ->add('tradedoublerKey', null, array(
                'required' => true,
                'label' => 'Tradedoubler key',
                'attr' => array('class' => 'network_tradedoubler_key'),
            ))
            ->add('tradedoublerUrl', null, array(
                'required' => true,
                'label' => 'Tradedoubler URL',
                'attr' => array('class' => 'network_tradedoubler_url'),
            ))
           /* ->add('webgainsTitle', null, array(
                'required' => true,
                'label' => 'Webgains Title',
                'attr' => array('class' => 'network_webgains_title'),
            ))*/
            ->add('webgainsUsername', null, array(
                'required' => true,
                'label' => 'Webgains Username',
                'attr' => array('class' => 'network_webgains_username'),
            ))
            ->add('webgainsPassword', null, array(
                'required' => true,
                'label' => 'Webgains Password',
                'attr' => array('class' => 'network_webgains_password'),
            ))
            ->add('webgainsCampaignId', null, array(
                'required' => true,
                'label' => 'Webgains Campaign Id',
                'attr' => array('class' => 'network_webgains_campaign_id'),
            ))
            ->add('webgainsLocation', null, array(
                'required' => true,
                'label' => 'Webgains Location',
                'attr' => array('class' => 'network_webgains_location'),
            ))
            ->add('webgainsUri', null, array(
                'required' => true,
                'label' => 'Webgains URL',
                'attr' => array('class' => 'network_webgains_uri'),
            ))
           /* ->add('zenoxTitle', null, array(
                'required' => true,
                'label' => 'Zanox Title',
                'attr' => array('class' => 'network_zanox_title'),
            ))*/
            ->add('zenoxConnectId', null, array(
                'required' => true,
                'label' => 'Zanox Connection Id',
                'attr' => array('class' => 'network_zanox_connection_id'),
            ))
            ->add('zenoxSecretKey', null, array(
                'required' => true,
                'label' => 'Zanox Secret Key',
                'attr' => array('class' => 'network_zanox_secret_key'),
            ))
            ->add('zenoxRegion', null, array(
                'required' => true,
                'label' => 'Zanox Region',
                'attr' => array('class' => 'network_zanox_region'),
            ))
            /*->add('amazonCredentials', 'sonata_type_collection', array(
                'cascade_validation' => true,
                'by_reference' => false,
                'label' => 'Variation\'s',
            ), array(
                    'edit' => 'inline',
                    'inline' => 'table',
                    'sortable' => 'position',
                    'link_parameters' => array('context' => 'default'),
                    'admin_code' => 'i_flair_lets_bonus_admin.admin.amazon_credentials',
                )
            )*/
        ;

        /*if ($subject instanceof AmazonCredentialsAdmin) {
            $formMapper->add('amazonCredentials', 'sonata_type_collection', array(
                'cascade_validation' => true,
                'by_reference' => false,
                'label' => 'Variation\'s',
            ), array(
                    'edit' => 'inline',
                    'inline' => 'table',
                    'sortable' => 'position',
                    'link_parameters' => array('context' => 'default'),
                    'admin_code' => 'i_flair_lets_bonus_admin.admin.amazon_credentials',
                )
            );
        }*/

        /*if(!$session->get('network_name')){
            $formMapper->remove('amazonCredentials');
        }*/
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('title')
            ->add('network.name')
            ->add('amazonUsername', 'string', array('template' => 'iFlairLetsBonusAdminBundle:Networks:amazon.html.twig'))
            ->add('amazonPassword')
            ->add('cjkey')
            ->add('cjurl')
            ->add('ebayUsername')
            ->add('ebayPassword')
            ->add('tdtiAffiliateId')
            ->add('tdtiKey')
            ->add('tdtiUrl')
            ->add('tradedoublerAffiliateId')
            ->add('tradedoublerKey')
            ->add('tradedoublerUrl')
            ->add('webgainsUsername')
            ->add('webgainsPassword')
            ->add('webgainsCampaignId')
            ->add('webgainsLocation')
            ->add('webgainsUri')
            ->add('zenoxConnectId')
            ->add('zenoxSecretKey')
            ->add('zenoxRegion')
        ;
    }

    /*public function prePersist($object)
    {
        $uniqid = $this->getRequest()->query->get('uniqid');
        $formData = $this->getRequest()->request->get($uniqid);
        var_dump($formData);exit;
    }*/
}
