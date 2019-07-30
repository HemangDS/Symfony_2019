<?php

namespace iFlair\LetsBonusAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use iFlair\LetsBonusAdminBundle\Entity\Settings;

class SettingsAdmin extends Admin
{
    protected $statusChoices = array(
            '' => 'Status',
            Settings::NO => 'No',
            Settings::YES => 'Yes',
    );

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('code')
            ->add('value')
            ->add('url')
            ->add('companies')
            ->add('description')
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
                ),
            ))
            ->add('code')
            ->add('value')
            ->add('url')
            ->addIdentifier('image', null, array(
                'template' => 'iFlairLetsBonusAdminBundle:Settings:picture.html.twig',
            ))
            ->add('companies')
            ->add('description')
            ->add('status', 'choice', array('choices' => $this->statusChoices))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $flagMediaImage = 1;
        $formMapper
            ->add('code')
            ->add('value', 'text', array('required' => false))
            ->add('url', 'url', array('required' => false));

        if (!$this->getRequest()->get($this->getIdParameter())) {
            $formMapper->add('image', 'sonata_media_type', array(
                'provider' => 'sonata.media.provider.image',
                'context' => 'default',
            ));
        } else {
            $image = $this->getSubject();

            if ($image) {
                $container = $this->getConfigurationPool()->getContainer();
                $em = $container->get('Doctrine');

                $settingsRepository = $em->getRepository('iFlairLetsBonusAdminBundle:Settings');

                $mediaImage = $image->getImage();

                if ($mediaImage) {
                    $flagMediaImage = 1;
                    $settingsMediaImagePreview = $settingsRepository->getMediaPreviewOverEditMode($mediaImage, $container);
                } else {
                    $flagMediaImage = 0;
                }
            }

            if ($flagMediaImage) {
                $formMapper->add('image', 'sonata_media_type', array(
                    'provider' => 'sonata.media.provider.image',
                    'context' => 'default',
                    'required' => false,
                    'help' => $settingsMediaImagePreview,
                ));
            } else {
                $formMapper->add('image', 'sonata_media_type', array(
                    'provider' => 'sonata.media.provider.image',
                    'context' => 'default',
                    'required' => false,
                ));
            }
        }

        $formMapper
            ->add('companies', 'entity', array(
                'class' => 'iFlair\LetsBonusAdminBundle\Entity\Companies',
                'property' => 'name',
            ))
            ->add('description')
            ->add('status', 'choice', array('choices' => $this->statusChoices))
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
            ->add('code')
            ->add('value')
            ->add('url')
            ->add('companies')
            ->add('description')
            ->add('status', 'choice', array('choices' => $this->statusChoices))
        ;
    }
}
