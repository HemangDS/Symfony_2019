<?php

namespace iFlair\LetsBonusAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class childCategoryAdmin extends Admin
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
            ->addIdentifier('name')
            ->add('url')
            ->addIdentifier('nimage', null, array(
                'template' => 'iFlairLetsBonusAdminBundle:Category:picture.html.twig',
            ))
            ->add('status')
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
        $formMapper
            ->add('parentCategory', 'entity', array(
                'class' => 'iFlair\LetsBonusAdminBundle\Entity\parentCategory',
                'property' => 'name',
            ))
            ->add('Category', 'shtumi_dependent_filtered_entity', array(
                'entity_alias' => 'categories_by_parentcategories',
                'parent_field' => 'parentCategory',
                'required' => true,
            ))
            ->add('name')
            ->add('url');

        if (!$this->getRequest()->get($this->getIdParameter())) {
            $formMapper->add('nimage', 'sonata_media_type', array(
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
                    $chilCategoryMediaNImagePreview = $settingsRepository->getMediaPreviewOverEditMode($mediaNImage, $container);
                } else {
                    $flagMediaNImage = 0;
                }
            }
            if ($flagMediaNImage) {
                $formMapper->add('nimage', 'sonata_media_type', array(
                    'provider' => 'sonata.media.provider.image',
                    'context' => 'default',
                    'required' => false,
                    'help' => $chilCategoryMediaNImagePreview,
                ));
            } else {
                $formMapper->add('nimage', 'sonata_media_type', array(
                    'provider' => 'sonata.media.provider.image',
                    'context' => 'default',
                    'required' => false,
                ));
            }
        }

        $formMapper
            ->add('status')
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
            ->add('status')
        ;
    }
}
