<?php

namespace iFlair\LetsBonusAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class SliderAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('image')
            ->add('title')
            ->add('description')
            ->add('enabled')
            ->add('slider_area')
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
            ->add('image')
            ->add('title')
            ->add('description')
            ->add('enabled')
            ->add('slider_area', 'choice', array('label' => 'Slider Area', 'choices' => array('homepage' => 'Homepage', 'como functiona top' => 'Como Functiona Top', 'como functiona middle' => 'Como Functiona Middle', 'como functiona bottom' => 'Como Functiona Bottom')))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title')
            ->add('enabled', 'choice', array('choices' => array('0' => 'Disabled', '1' => 'Enabled')))
            ->add('slider_area', 'choice', array('label' => 'Select Slider Area', 'choices' => array('homepage' => 'Homepage', 'como functiona top' => 'Como Functiona Top', 'como functiona middle' => 'Como Functiona Middle', 'como functiona bottom' => 'Como Functiona Bottom')));

        if (!$this->getRequest()->get($this->getIdParameter())) {
            $formMapper->add('image', 'sonata_media_type', array(
                'provider' => 'sonata.media.provider.image',
                'context' => 'default',
            ));
        } else {
            $formMapper->add('image', 'sonata_media_type', array(
                'provider' => 'sonata.media.provider.image',
                'context' => 'default',
                'required' => false,
            ));
        }

        $formMapper
            ->add('url', 'url', array('required' => false))
            ->add('description', 'textarea', array('required' => false, 'attr' => array('class' => 'ckeditor')))
            ->add('show_in_front', 'choice', array('choices' => array('0' => 'No', '1' => 'Yes')))
            ->add('start_date',   'text', array('attr' => array('class' => 'start_date')))
            ->add('end_date',   'text', array('attr' => array('class' => 'end_date')));
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('image')
            ->add('title')
            ->add('description')
            ->add('enabled')
            ->add('slider_area')
        ;
    }

    public function configure()
    {
        $this->setTemplate('edit', 'iFlairLetsBonusAdminBundle:CRUD:datpicker.html.twig');
    }
}
