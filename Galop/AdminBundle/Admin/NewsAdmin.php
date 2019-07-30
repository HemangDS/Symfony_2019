<?php

namespace Galop\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;


class NewsAdmin extends AbstractAdmin
{
	
	/**
     * @param FormMapper $formMapper
     */
	protected function configureFormFields(FormMapper $formMapper)
    {
        $container = $this->getConfigurationPool()->getContainer();
        $em = $container->get('doctrine');
        $newsObj = $this->getSubject();
        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();

        $formMapper
            ->tab('General')
                ->with('News Data', ['class' => 'col-md-9'])->end()
            ->end()
        ;

        $formMapper
            ->tab('General')
                ->with('News Data')
                    ->add('title', 'text', array('label' => 'Title')) 
                    ->add('fullArticle', CKEditorType::class, array(
                                'label' => 'Full Article',
                                'attr' => array("class" => "full_article"),
                            )
                        )
                ->end()
            ->end()
        ;


        if (!$this->getRequest()->get($this->getIdParameter())) {

            $formMapper
                ->tab('General')
                    ->with('News Data')
                        ->add('shortArticle', 'textarea', array('label' => 'Short Article','attr' => array(
                                    "class" => "short_article", 
                                    'readonly' => true,
                                )
                            )
                        )
                   ->end()
                ->end()
            ;
            $formMapper
                ->tab('General')
                    ->with('News Data')
                        ->add('images', 'sonata_type_model_list', array('label' => 'Image', 'required' => false) , array (
                                'link_parameters' => array('provider' => 'sonata.media.provider.image'),
                                'label' => 'Image',
                            ), array (
                                    'edit' => 'inline',
                                    'inline' => 'table',
                                    'sortable' => 'position',
                                    'link_parameters' => array('provider' => 'sonata.media.provider.image'),
                                    'admin_code' => 'sonata.media.admin.gallery',
                            )
                        )
                    ->end()
                ->end()
            ;
        } else {

            $formMapper
                ->tab('General')
                    ->with('News Data')
                        ->add('shortArticle', 'textarea', array('label' => 'Short Article')
                    )
                   ->end()
                ->end()
            ;

            if ($newsObj) {
                $newsRepository = $em->getRepository('GalopAdminBundle:News');
                $newsImage = $newsObj->getImages();
                $size = "default_preview";
                if ($newsImage) {
                    $formMapper
                        ->tab('General')
                            ->with('News Data')
                                ->add('images', 'sonata_type_model_list', array('label' => 'Image', 'required' => false) ,array(
                                    'link_parameters' => array('provider' => 'sonata.media.provider.image'),
                                    'label' => 'Image',
                                ), array (
                                    'edit' => 'inline',
                                    'inline' => 'table',
                                    'sortable' => 'position',
                                    'link_parameters' => array('provider' => 'sonata.media.provider.image'),
                                    'admin_code' => 'sonata.media.admin.gallery',
                                ))
                            ->end()
                        ->end()
                    ;
                } else {
                    $formMapper
                        ->tab('General')
                            ->with('News Data')
                                ->add('images', ModelType::class, array('label' => 'Image', 'required' => false) ,array(
                                    'link_parameters' => array('provider' => 'sonata.media.provider.image'),
                                    'label' => 'Image',
                                ), array (
                                        'edit' => 'inline',
                                        'inline' => 'table',
                                        'sortable' => 'position',
                                        'link_parameters' => array('provider' => 'sonata.media.provider.image'),
                                        'admin_code' => 'sonata.media.admin.gallery',
                                    )
                                )
                            ->end()
                        ->end()
                    ;
                }
            }
        }
        $formMapper
            ->tab('General')
                ->with('News Data')
                    ->add('document', "sonata_type_model_list", array('label' => 'Document') ,array(
                                'link_parameters' => array('provider' => 'sonata.media.provider.file'),
                                'data_class'   =>  'Application\Sonata\MediaBundle\Entity\Media',
                                'context' => 'default',
                                'label' => 'Document',
                            )
                        )
                    ->add('category', ModelType::class, [
                            'class' => 'Galop\AdminBundle\Entity\NewsCategory',
                            'property' => 'category',
                            'multiple' => false,
                            'expanded' => false,
                            'placeholder'=> false,
                            'preferred_choices' => array('1')
                        ]
                    )
                    ->add('descipline', ModelType::class, [
                            'class' => 'Galop\AdminBundle\Entity\NewsDescipline',
                            'property' => 'descipline',
                            'multiple' => false,
                            'expanded' => false,
                            'placeholder'=> false,
                            'label' => "Discipline",
                            'preferred_choices' => array('6')
                        ]
                    )
                    ->add('region', ModelType::class, [
                            'class' => 'Galop\AdminBundle\Entity\NewsRegion',
                            'property' => 'region',
                            'multiple' => false,
                            'expanded' => false,
                            'placeholder'=> false,
                            'preferred_choices' => array('3')
                        ]
                    )
                    ->add('tags', 'sonata_type_model_autocomplete', array(
                            'label' => 'Tags',
                            'class' => 'Galop\AdminBundle\Entity\Tag',
                            'multiple' =>true,
                            'property' => 'title',
                            'required' => false,
                            'btn_add' => "Add New Tag"
                        )
                    )
                    ->add('watermarkText', 'text', array(
                            'label' => 'Watermark Text',
                            'required' => false,
                            'empty_data' => 'Paardenfotograaf.be',
                            'attr' => ['placeholder' => 'Paardenfotograaf.be']
                        )
                    )
                    ->add('photographer', 'text', array(
                            'label' => 'Photographer Text',
                            'required' => false,
                            'empty_data' => 'Paardenfotograaf.be',
                            'attr' => ['placeholder' => 'Paardenfotograaf.be']
                        )
                    )
                ->end()
            ->end()
        ; 
        if ($this->getRequest()->get($this->getIdParameter())) {
            $formMapper
                ->tab('General')
                    ->with('News Data')
                        ->add('articleDate', DateTimeType::class, array(
                                'label' => 'Article Date',
                                'date_widget' => 'single_text',
                                'time_widget' => 'single_text',
                                'with_seconds' => true,
                            )
                        )
                        ->add('author', 'text', array('label' => 'Author'))
                    ->end()
                ->end()
            ;
        } else {
            $name = '';
            if(!empty($user->getFirstname()))
                $name.= $user->getFirstname();
            if(!empty($user->getLastname()))
                if(!empty($name))
                    $name.= " " .$user->getLastname();
                else
                    $name.= $user->getLastname();
            $formMapper
                ->tab('General')
                    ->with('News Data')
                        ->add('articleDate', DateTimeType::class, array(
                                'label' => 'Article Date',
                                'date_widget' => 'single_text',
                                'time_widget' => 'single_text',
                                'with_seconds' => true,
                                'data' => new \DateTime('now')
                            )
                        )
                        ->add('author', 'text', array('label' => 'Author', 'data' => $name))
                    ->end()
                ->end()
            ;
        }

        $formMapper
            ->tab('General')
                ->with('News Data')
                    ->add('desktop_counter', 'integer', array(
                            'label' => 'Desktop Clicks',
                            'required' => false,
                            'attr' => array(
                                'readonly' => true,
                            )
                        )
                    )
                    ->add('mobile_counter', 'integer', array(
                            'label' => 'Mobile Clicks',
                            'required' => false,
                            'attr' => array(
                                'readonly' => true,
                            )
                        )
                    )
                    ->add('tablet_counter', 'integer', array(
                            'label' => 'Tablet Clicks',
                            'required' => false,
                            'attr' => array(
                                'readonly' => true,
                            )
                        )
                    )
                    ->add('status', null, array('label' => 'Status'))
                    ->add('isHeadline', null, array('label' => 'Headline'))
                    ->add('isPremium', null, array('label' => 'Premium'))
                ->end()
            ->end()
        ;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
                ->add('title', null, array('label' => 'Title'))
                ->add('shortArticle', null, array('label' => 'Short Article'))
                ->add('images')
                ->add('category', null, array('label' => 'Category'))
                ->add('descipline', null, array('label' => 'Discipline'))
                ->add('region', null, array('label' => 'Region'))
                ->add('tags')
                ->add('watermarkText')
                ->add('author')
                ->add('created', 'doctrine_orm_date_range', array('label' => 'Created', 'widget' => 'single_text'))
                ->add('createdByUser')
                ->add('updatedByUser')
                ->add('status')
                ->add('isHeadline')
                ->add('isPremium')
                ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
                ->add('title', null, array('label' => 'Title'))
                ->addIdentifier('images', null, array(
                    'template' => 'GalopAdminBundle:News:picture.html.twig',
                    'label' => 'Image',
                ))
                ->add('category', null, array('label' => 'Category'))
                ->add('region', null, array('label' => 'Region'))
                ->add('tags')
                ->add('author')
                ->add('desktop_counter', null, ['label' => 'Desktop Clicks'])
                ->add('mobile_counter', null, ['label' => 'Mobile Clicks'])
                ->add('tablet_counter', null, ['label' => 'Tablet Clicks'])
                ->add('status')
                ->add('isHeadline')
                ->add('isPremium')
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
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('title', null, array('label' => 'Title'))
            ->add('shortArticle', null, array('label' => 'Short Article'))
            ->add('images', null, array(
                'template' => 'GalopAdminBundle:News:picture.html.twig',
                'label' => 'Image',
            ))
            ->add('category', null, array('label' => 'Category'))
            ->add('descipline', null, array('label' => 'Descipline'))
            ->add('region', null, array('label' => 'Region'))
            ->add('tags')
            ->add('watermarkText')
            ->add('author')
            ->add('created')
            ->add('createdByUser')
            ->add('updatedByUser')
            ->add('desktop_counter', null, ['label' => 'Desktop Clicks'])
            ->add('mobile_counter', null, ['label' => 'Mobile Clicks'])
            ->add('tablet_counter', null, ['label' => 'Tablet Clicks'])
            ->add('status')
            ->add('isHeadline')
            ->add('isPremium')
        ;
    }
}