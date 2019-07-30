<?php

namespace iFlair\LetsBonusAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class ThreeStepAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('step')
            ->add('title')
            ->add('image')
            ->add('description')
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
            ->add('step')
            ->addIdentifier('title')
            ->addIdentifier('image', null, array(
                'template' => 'iFlairLetsBonusAdminBundle:ThreeStep:picture.html.twig',
            ))
            ->add('description')
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
            ->add('step', 'choice', array('choices' => array('1' => 'Step One', '2' => 'Step Two', '3' => 'Step Three')))
            ->add('title');
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
                    $threeStepMediaImagePreview = $settingsRepository->getMediaPreviewOverEditMode($mediaImage, $container);
                } else {
                    $flagMediaImage = 0;
                }
            }
            if ($flagMediaImage) {
                $formMapper->add('image', 'sonata_media_type', array(
                    'provider' => 'sonata.media.provider.image',
                    'context' => 'default',
                    'required' => false,
                    'help' => $threeStepMediaImagePreview,
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
            ->add('description')
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
            ->add('step')
            ->add('title')
            ->add('image')
            ->add('description')
            ->add('status')
            ->add('startDate')
            ->add('endDate')
            ->add('created')
            ->add('modified')
        ;
    }
}
