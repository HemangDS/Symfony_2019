<?php

namespace iFlair\LetsBonusAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class parentCategoryAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name')
            ->add('url')
            ->add('nimage')
            ->add('bannerimage')
            ->add('logoimage')
            ->add('status')
            ->add('show_on_como_functiona')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->addIdentifier('name')
            ->add('url')
            ->addIdentifier('nimage', null, array(
                'template' => 'iFlairLetsBonusAdminBundle:Category:picture.html.twig',
            ))
            ->addIdentifier('bannerimage', null, array(
                'template' => 'iFlairLetsBonusAdminBundle:Category:banner.html.twig',
            ))
            ->addIdentifier('logoimage', null, array(
                'template' => 'iFlairLetsBonusAdminBundle:Category:logoimage.html.twig',
            ))
            ->add('status')
            ->add('show_on_como_functiona')
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
        $flagMediaNImage = 1;
        $flagMediaBannerImage = 1;
        $formMapper
            ->add('name')
            ->add('url','text',array('required' => true))
            ->add('show_on_como_functiona', 'choice', array(
                'choices' => array('0' => 'No', '1' => 'Yes'
            )));

        if (!$this->getRequest()->get($this->getIdParameter())) {
            $formMapper->add('nimage', 'sonata_media_type', array(
                    'provider' => 'sonata.media.provider.image',
                    'context' => 'default',
            ));
            $formMapper->add('bannerimage', 'sonata_media_type', array(
                'provider' => 'sonata.media.provider.image',
                'context' => 'default',
            ));
            $formMapper->add('logoimage', 'sonata_media_type', array(
                'provider' => 'sonata.media.provider.image',
                'context' => 'default',
            ));
        } else {
            $image = $this->getSubject();

            if ($image) {
                $container = $this->getConfigurationPool()->getContainer();
                $em = $container->get('Doctrine');

                $settingsRepository = $em->getRepository('iFlairLetsBonusAdminBundle:Settings');

                $mediaNImage = $image->getnImage();
                if ($mediaNImage) {
                    $flagMediaNImage = 1;
                    $parentCategoryMediaNImagePreview = $settingsRepository->getMediaPreviewOverEditMode($mediaNImage, $container);
                } else {
                    $flagMediaNImage = 0;
                }

                $mediaBannerImage = $image->getBannerImage();
                if ($mediaBannerImage) {
                    $parentCategoryMediaBannerImagePreview = $settingsRepository->getMediaPreviewOverEditMode($mediaBannerImage, $container);
                    $flagMediaBannerImage = 1;
                } else {
                    $flagMediaBannerImage = 0;
                }

                $logoImage = $image->getLogoImage();
                if ($logoImage) {
                    $parentCategoryLogoImagePreview = $settingsRepository->getMediaPreviewOverEditMode($logoImage, $container);
                    $flagLogoImage = 1;
                } else {
                    $flagLogoImage = 0;
                }
            }
            if ($flagMediaNImage) {
                $formMapper->add('nimage', 'sonata_media_type', array(
                    'provider' => 'sonata.media.provider.image',
                    'context' => 'default',
                    'required' => false,
                    'help' => $parentCategoryMediaNImagePreview,
                ));
            } else {
                $formMapper->add('nimage', 'sonata_media_type', array(
                    'provider' => 'sonata.media.provider.image',
                    'context' => 'default',
                    'required' => false,
                ));
            }
            if ($flagMediaBannerImage) {
                $formMapper->add('bannerimage', 'sonata_media_type', array(
                    'provider' => 'sonata.media.provider.image',
                    'context' => 'default',
                    'required' => false,
                    'help' => $parentCategoryMediaBannerImagePreview,
                ));
            } else {
                $formMapper->add('bannerimage', 'sonata_media_type', array(
                    'provider' => 'sonata.media.provider.image',
                    'context' => 'default',
                    'required' => false,
                ));
            }
            if ($flagLogoImage) {
                $formMapper->add('logoimage', 'sonata_media_type', array(
                    'provider' => 'sonata.media.provider.image',
                    'context' => 'default',
                    'required' => false,
                    'help' => $parentCategoryLogoImagePreview,
                ));
            } else {
                $formMapper->add('logoimage', 'sonata_media_type', array(
                    'provider' => 'sonata.media.provider.image',
                    'context' => 'default',
                    'required' => false,
                ));
            }
        }

        $formMapper
            ->add('status')
            ->add('highlightedHome', 'choice', array('choices' => array('0' => 'No', '1' => 'Yes')))
             ->add('bannertitle')
            ->add('bannerdescription', 'textarea')
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
            ->add('url')
            ->add('nimage')
            ->add('bannerimage')
            ->add('logoimage')
            ->add('status')
            ->add('show_on_como_functiona')
        ;
    }
}
