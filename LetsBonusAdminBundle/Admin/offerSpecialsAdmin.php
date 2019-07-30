<?php

namespace iFlair\LetsBonusAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class offerSpecialsAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('url')
            ->add('image')
            ->add('status')
            ->add('startDate')
            ->add('endDate')
            ->add('created')
            ->add('modified')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('url')
            ->addIdentifier('image', null, array(
                'template' => 'iFlairLetsBonusAdminBundle:offerSpecials:picture.html.twig',
            ))
            ->add('status')
            ->add('startDate')
            ->add('endDate')
            ->add('created')
            ->add('modified')
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
        $formMapper
            ->add('voucherProgramsId', 'sonata_type_model_autocomplete', array(
                'property' => 'programName',
                'label' => 'Select shop',
                'class' => 'iFlairLetsBonusAdminBundle:VoucherPrograms',
                'attr' => ['style' => 'width: 100%;'],
                'to_string_callback' => function ($entity, $property) {
                    return $entity->getProgramName();
                },
            ));
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
                    $offerSpecialsMediaImagePreview = $settingsRepository->getMediaPreviewOverEditMode($mediaImage, $container);
                } else {
                    $flagMediaImage = 0;
                }
            }
            if ($flagMediaImage) {
                $formMapper->add('image', 'sonata_media_type', array(
                    'provider' => 'sonata.media.provider.image',
                    'context' => 'default',
                    'required' => false,
                    'help' => $offerSpecialsMediaImagePreview,
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
            ->add('url', 'url', array('required' => false))
            ->add('status', 'choice', array('choices' => array('0' => 'Disable', '1' => 'Enable')))
            ->add('startDate', 'sonata_type_datetime_picker', array('label' => 'Start date', 'dp_language' => 'de', 'format' => 'yyyy-MM-dd hh:mm:ss', 'read_only' => true, 'attr' => array('data-date-format' => 'yyyy-MM-dd hh:mm:ss')))
            ->add('endDate',   'sonata_type_datetime_picker', array('label' => 'End date', 'dp_language' => 'en', 'format' => 'yyyy-MM-dd hh:mm:ss', 'read_only' => true))
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('url')
            ->add('image')
            ->add('status')
            ->add('startDate')
            ->add('endDate')
            ->add('created')
            ->add('modified')
        ;
    }
}
