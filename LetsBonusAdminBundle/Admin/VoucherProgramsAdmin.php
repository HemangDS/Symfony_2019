<?php

namespace iFlair\LetsBonusAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class VoucherProgramsAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('nprogramId')
            ->add('programName')
            ->add('network')
            ->add('logoPath')
            ->add('image')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('nprogramId')
            ->add('programName')
            ->add('logoPath')
            ->add('network')
            ->addIdentifier('image', null, array(
                'template' => 'iFlairLetsBonusAdminBundle:VoucherPrograms:picture.html.twig',
            ))
            ->addIdentifier('banner', null, array(
                'template' => 'iFlairLetsBonusAdminBundle:VoucherPrograms:picture1.html.twig',
            ))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                ),
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $flagMediaImage = 1;
        $flagBannerImage = 1;
        $flagRightBlockImage = 1;
        $flagPopUpImage = 1;
        $formMapper
            ->add('nprogramId')
            ->add('programName')
            ->add('logoPath', null, array('required' => false))
            ->add('network', 'entity', array(
                'class' => 'iFlair\LetsBonusAdminBundle\Entity\Network',
                'property' => 'name',
            ));
        if (!$this->getRequest()->get($this->getIdParameter())) {
            $formMapper->add('image', 'sonata_media_type', array(
                    'provider' => 'sonata.media.provider.image',
                    'context' => 'default',
                ));
            $formMapper->add('banner', 'sonata_media_type', array(
                    'provider' => 'sonata.media.provider.image',
                    'context' => 'default',
                ));
            $formMapper->add('rightBlockImage', 'sonata_media_type', array(
                    'provider' => 'sonata.media.provider.image',
                    'context' => 'default',
                ));
            $formMapper->add('popUpImage', 'sonata_media_type', array(
                    'provider' => 'sonata.media.provider.image',
                    'context' => 'default',
                ));
        } else {
            $image = $this->getSubject();
            $flagMediaImage = 0;
            $flagBannerImage = 0;
            $flagRightBlockImage = 0;
            $flagPopUpImage = 0;
            if ($image) {
                $container = $this->getConfigurationPool()->getContainer();
                $em = $container->get('Doctrine');

                $settingsRepository = $em->getRepository('iFlairLetsBonusAdminBundle:Settings');

                $mediaImage = $image->getImage();
                $bannerImage = $image->getBanner();
                $rightBlockImage = $image->getRightBlockImage();
                $popUpImage = $image->getPopUpImage();
                if ($mediaImage) {
                    $flagMediaImage = 1;
                    $voucherProgramsAdminMediaImagePreview = $settingsRepository->getMediaPreviewOverEditMode($mediaImage, $container);
                }
                if ($bannerImage) {
                    $flagBannerImage = 1;
                    $voucherProgramsAdminMediaBannerPreview = $settingsRepository->getMediaPreviewOverEditMode($bannerImage, $container);
                }
                if ($rightBlockImage) {
                    $flagRightBlockImage = 1;
                    $voucherProgramsAdminMediaRightBlockPreview = $settingsRepository->getMediaPreviewOverEditMode($rightBlockImage, $container);
                }
                if ($popUpImage) {
                    $flagPopUpImage = 1;
                    $voucherProgramsAdminMediaPopUpPreview = $settingsRepository->getMediaPreviewOverEditMode($popUpImage, $container);
                }
            }
            if ($flagMediaImage) {
                $formMapper->add('image', 'sonata_media_type', array(
                    'provider' => 'sonata.media.provider.image',
                    'context' => 'default',
                    'required' => false,
                    'help' => $voucherProgramsAdminMediaImagePreview,
                ));
            } else {
                $formMapper->add('image', 'sonata_media_type', array(
                    'provider' => 'sonata.media.provider.image',
                    'context' => 'default',
                    'required' => false,
                ));
            }

            if ($flagBannerImage) {
                $formMapper->add('banner', 'sonata_media_type', array(
                    'provider' => 'sonata.media.provider.image',
                    'context' => 'default',
                    'required' => false,
                    'help' => $voucherProgramsAdminMediaBannerPreview,
                ));
            } else {
                $formMapper->add('banner', 'sonata_media_type', array(
                    'provider' => 'sonata.media.provider.image',
                    'context' => 'default',
                    'required' => false,
                ));
            }

            if($flagRightBlockImage){
                $formMapper->add('rightBlockImage', 'sonata_media_type', array(
                    'provider' => 'sonata.media.provider.image',
                    'context' => 'default',
                    'required' => false,
                    'help' => $voucherProgramsAdminMediaRightBlockPreview,
                ));
            } else{
                $formMapper->add('rightBlockImage', 'sonata_media_type', array(
                    'provider' => 'sonata.media.provider.image',
                    'context' => 'default',
                    'required' => false,
                ));
            }

            if($flagPopUpImage){
                $formMapper->add('popUpImage', 'sonata_media_type', array(
                    'provider' => 'sonata.media.provider.image',
                    'context' => 'default',
                    'required' => false,
                    'help' => $voucherProgramsAdminMediaPopUpPreview,
                ));
            } else{
                $formMapper->add('popUpImage', 'sonata_media_type', array(
                    'provider' => 'sonata.media.provider.image',
                    'context' => 'default',
                    'required' => false,
                ));
            }
        }
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('nprogramId')
            ->add('programName')
            ->add('logoPath')
            ->add('network')
            ->add('image')
            ->add('banner')
        ;
    }
}
