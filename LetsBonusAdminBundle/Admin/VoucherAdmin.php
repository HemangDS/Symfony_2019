<?php

namespace iFlair\LetsBonusAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use iFlair\LetsBonusAdminBundle\Entity\Voucher;

class VoucherAdmin extends Admin
{
    protected $voucherTypeIds = array(
            '' => 'Select voucher type',
            Voucher::COUPON => 'Cupón',
            Voucher::DISCOUNT => 'Descuento',
            Voucher::FREEARTICLES => 'Artículos gratis',
            Voucher::FREESHIPPING => 'Envío Gratis',
            Voucher::DRAW => 'Sorteo',
        );

    protected $isPercentageChoices = array(
            '' => 'Is percentage?',
            Voucher::NO => 'No',
            Voucher::YES => 'Yes',
        );

    protected $isExclusiveChoices = array(
            '' => 'Is exclusive?',
            Voucher::NO => 'No',
            Voucher::YES => 'Yes',
    );

    protected $isNewChoices = array(
            '' => 'Is new?',
            Voucher::NO => 'No',
            Voucher::YES => 'Yes',
    );

    protected $isSiteSpecificChoices = array(
            '' => 'Is site specific?',
            Voucher::NO => 'No',
            Voucher::YES => 'Yes',
    );

    protected $statusChoices = array(
            '' => 'Status',
            Voucher::VOUCHERDEACTIVE => 'Deactive',
            Voucher::VOUCHERACTIVE => 'Active',
            Voucher::VOUCHEREXPIRED => 'Expired',
    );

    protected $isDisplayOnFront = array(
            Voucher::NO => 'No',
            Voucher::YES => 'Yes',
    );

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('title')
            ->add('code')
            ->add('program')
            ->add('network')
            //->add('voucherTypeId', null, array(), 'doctrine_orm_choice', array('choices' => $this->voucherTypeIds))
            //->add('voucherTypeId','choice',array('choices' => $this->voucherTypeIds))
            //->add('voucherTypeId')
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
            ->add('code')
            ->add('program')
            ->add('discountAmount')
            ->add('isPercentage', 'choice', array('choices' => $this->isPercentageChoices))
            ->add('voucherTypeId', 'choice', array('choices' => $this->voucherTypeIds))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('refVoucherId')
            ->add('code', null, array('required' => false))
            ->add('title')
            ->add('shortDescription', null, array('required' => false))
            ->add('description')
            ->add('voucherTypeId', 'choice', array('choices' => $this->voucherTypeIds))
            ->add('defaultTrackUri')
            ->add('siteSpecific', 'choice', array('choices' => $this->isSiteSpecificChoices))
            ->add('landingUrl', null, array('required' => false))
            ->add('discountAmount', null, array('required' => false))
            ->add('isPercentage', 'choice', array('choices' => $this->isPercentageChoices))
            ->add('publisherInfo', null, array('required' => false))
            ->add('exclusive', 'choice', array('choices' => $this->isExclusiveChoices))
            ->add('isnew', 'choice', array('choices' => $this->isNewChoices))
            ->add('status', 'choice', array('choices' => $this->statusChoices))
            ->add('isDisplayOnFront', 'choice', array('choices' => $this->isDisplayOnFront))
            ->add('network', 'entity', array(
                'class' => 'iFlair\LetsBonusAdminBundle\Entity\Network',
                'property' => 'name',
            ))
            /*->add('program', 'entity', array(
                'class' => 'iFlair\LetsBonusAdminBundle\Entity\VoucherPrograms',
                'property' => 'nprogramId',
            ))*/
            ->add('program', 'shtumi_dependent_filtered_entity', array(
                'entity_alias' => 'program_by_network',
                'parent_field' => 'network',
                'required' => true,
            ))
            ->add('language', 'entity', array(
                'class' => 'iFlair\LetsBonusAdminBundle\Entity\Language',
                'property' => 'code',
            ))
            ->add('currency', 'entity', array(
                'class' => 'iFlair\LetsBonusAdminBundle\Entity\Currency',
                'property' => 'code',
            ))
            ->add('publishStartDate', 'sonata_type_datetime_picker', array('label' => 'Start date', 'dp_language' => 'en', 'format' => 'yyyy-MM-dd hh:mm:ss', 'read_only' => true))
            ->add('publishEndDate',   'sonata_type_datetime_picker', array('label' => 'End date', 'dp_language' => 'en', 'format' => 'yyyy-MM-dd hh:mm:ss', 'read_only' => true))
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('code')
            ->add('publishStartDate')
            ->add('publishEndDate')
            ->add('title')
            ->add('shortDescription')
            ->add('description')
            ->add('voucherTypeId', 'choice', array('choices' => $this->voucherTypeIds))
            ->add('defaultTrackUri')
            ->add('siteSpecific', 'choice', array('choices' => $this->isSiteSpecificChoices))
            ->add('landingUrl', null, array('required' => false))
            ->add('discountAmount')
            ->add('isPercentage', 'choice', array('choices' => $this->isPercentageChoices))
            ->add('publisherInfo', null, array('required' => false))
            ->add('exclusive', 'choice', array('choices' => $this->isExclusiveChoices))
            ->add('isnew', 'choice', array('choices' => $this->isNewChoices))
            ->add('status', 'choice', array('choices' => $this->statusChoices))
            ->add('program', 'entity', array(
                'class' => 'iFlair\LetsBonusAdminBundle\Entity\VoucherPrograms',
                'property' => 'programId',
            ))
            ->add('network', 'entity', array(
                'class' => 'iFlair\LetsBonusAdminBundle\Entity\Network',
                'property' => 'name',
            ))
            ->add('currency', 'entity', array(
                'class' => 'iFlair\LetsBonusAdminBundle\Entity\Currency',
                'property' => 'code',
            ))
        ;
    }
}
