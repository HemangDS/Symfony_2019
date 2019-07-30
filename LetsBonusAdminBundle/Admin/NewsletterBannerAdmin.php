<?php

namespace iFlair\LetsBonusAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class NewsletterBannerAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('bannername')
            ->add('title')
            ->add('dfpcode')
            ->add('url')
            ->add('image')
            ->add('firstbanner')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('bannername')
            ->add('title')
            ->add('dfpcode')
            ->add('url')
            ->addIdentifier('image', null, array(
                'template' => 'iFlairLetsBonusAdminBundle:NewsletterBanner:newsletterbanner.html.twig',
            ))
            ->add('firstbanner')
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
        $formMapper
            ->add('title')
            ->add('bannername')

            ->add('dfpcode', 'textarea', array('required' => false, 'attr' => array('class' => 'ckeditor')))

            ->add('url', 'url', array('required' => false));

        if (!$this->getRequest()->get($this->getIdParameter())) {
            $formMapper->add('image', 'sonata_media_type', array(
                    'provider' => 'sonata.media.provider.image',
                    'context' => 'default',
                ));
        } else {
            $bannerimage = $this->getSubject();
            if ($bannerimage) {
                $container = $this->getConfigurationPool()->getContainer();
                $em = $container->get('Doctrine');
                $settingsRepository = $em->getRepository('iFlairLetsBonusAdminBundle:Settings');
                $mediaImage = $bannerimage->getImage();
                $shopMediaImagePreview = $settingsRepository->getMediaPreviewOverEditMode($mediaImage, $container);
            }

            $formMapper->add('image', 'sonata_media_type', array(
                    'provider' => 'sonata.media.provider.image',
                    'context' => 'default',
                    'required' => false,
                    'help' => $shopMediaImagePreview,
                ));
        }
        $formMapper->add('firstbanner', 'choice', array('label' => 'Is This Main(First) Banner In Email Template..??', 'choices' => array('0' => 'NO', '1' => 'YES')))
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('bannername')
            ->add('title')
            ->add('dfpcode')
            ->add('url')
            ->add('image')
            ->add('firstbanner')
        ;
    }
}
