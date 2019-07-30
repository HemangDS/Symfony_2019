<?php

namespace iFlair\LetsBonusAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class AdvertisementAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('advName')
            ->add('image')
            ->add('advType')
            ->add('status')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->addIdentifier('advName')
            ->addIdentifier('image', null, array(
                'template' => 'iFlairLetsBonusAdminBundle:Advertisement:picture.html.twig',
            ))
            ->addIdentifier('advType.advTypeName')
            ->add('status')
            ->add('created', 'datetime', array('label' => 'Created', 'pattern' => 'yyyy-MM-dd hh:mm:ss', 'locale' => 'en'))
            ->add('modified', 'datetime', array('label' => 'Modified', 'pattern' => 'yyyy-MM-dd hh:mm:ss', 'locale' => 'en'))
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
            ->add('advName');

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
                    $advertisementMediaImagePreview = $settingsRepository->getMediaPreviewOverEditMode($mediaImage, $container);
                } else {
                    $flagMediaImage = 0;
                }
            }
            if ($flagMediaImage) {
                $formMapper->add('image', 'sonata_media_type', array(
                    'provider' => 'sonata.media.provider.image',
                    'context' => 'default',
                    'required' => false,
                    'help' => $advertisementMediaImagePreview,
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
            ->add('advType', 'entity', array(
                        'class' => 'iFlair\LetsBonusAdminBundle\Entity\AdvertisementType',
                        'property' => 'advTypeName',
                    ))
            ->add('status')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('advName')
            ->add('image')
            ->add('advType')
            ->add('status')
        ;
    }

    /**
     * Default Datagrid values.
     *
     * @var array
     */
    protected $datagridValues = array(
        '_per_page' => 1,
        '_page' => 1,
        '_sort_order' => 'DESC', // reverse order (default = 'ASC')
        '_sort_by' => 'created',  // name of the ordered field
        // (default = the model's id field, if any)

        // the '_sort_by' key can be of the form 'mySubModel.mySubSubModel.myField'.
    );
}
