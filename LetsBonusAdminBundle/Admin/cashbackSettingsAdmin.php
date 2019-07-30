<?php

namespace iFlair\LetsBonusAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Form\Type\EqualType;
use Sonata\CoreBundle\Form\Type\BooleanType;
use iFlair\LetsBonusAdminBundle\Entity\Shop;
use iFlair\LetsBonusAdminBundle\Entity\parentCategory;
use Sonata\AdminBundle\Route\RouteCollection;

class cashbackSettingsAdmin extends Admin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection->add('loadParentCategory', 'loadParentCategory');
    }


     public function getTemplate($name)
    {
        switch ($name) {
            case 'edit':
                return 'iFlairLetsBonusAdminBundle:CashbackSettings:parent.html.twig';
                break;
            default:
                return parent::getTemplate($name);
                break;
        }
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name', null, array('label' => 'Title', 'show_filter' => true))
            ->add('companies.name')
            ->add('type')
            ->add('startDate')
            ->add('endDate')
            ->add('status')
            ->add('shop')
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
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                ),
            ))
            ->add('id')
            ->add('companies.name')
            ->add('name','text',array('label' => 'Title'))
            ->add('type')
            ->add('status')
            ->add('startDate', 'datetime', array('label' => 'Start date', 'pattern' => 'yyyy-MM-dd hh:mm:ss', 'locale' => 'en', 'timezone' => 'Europe/Paris'))
            ->add('endDate', 'datetime', array('label' => 'End date', 'pattern' => 'yyyy-MM-dd hh:mm:ss', 'locale' => 'en', 'timezone' => 'Europe/Paris'))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name','text',array('label' => 'Title'))
            ->add('companies', 'entity', array(
                'class' => 'iFlair\LetsBonusAdminBundle\Entity\Companies',
                'property' => 'name',
            ))
            ->add('type', 'choice', array('choices' => array('' => 'Select a type', 'double' => 'Double', 'triple' => 'Triple')))
            ->add('status', 'choice', array('choices' => array('' => 'Select a status', '0' => 'Deactivated', '1' => 'Activated')))
            ->add('administrator', 'entity', array(
                'class' => 'Application\Sonata\UserBundle\Entity\User',
                'property' => 'username',
            ))
            ->add('startDate', 'sonata_type_datetime_picker', array('label' => 'Start date', 'dp_language' => 'de', 'format' => 'yyyy-MM-dd hh:mm:ss', 'read_only' => true, 'attr' => array('data-date-format' => 'yyyy-MM-dd hh:mm:ss')))
            ->add('endDate',   'sonata_type_datetime_picker', array('label' => 'End date', 'dp_language' => 'en', 'format' => 'yyyy-MM-dd hh:mm:ss', 'read_only' => true))
             ->add('shop', 'entity', array(
               'class' => 'iFlair\LetsBonusAdminBundle\Entity\Shop',
                'property' => 'title',
                'multiple' => true,
                'expanded' => true,
                'attr' => array('class' => 'cashback_settings_shop_title'),
            ))
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name','text',array('label' => 'Title'))
            ->add('companies.name')
            ->add('type')
            ->add('status')
            ->add('startDate', 'datetime', array('label' => 'Start date', 'pattern' => 'yyyy-MM-dd hh:mm:ss', 'locale' => 'en', 'timezone' => 'Europe/Paris'))
            ->add('endDate', 'datetime', array('label' => 'End date', 'pattern' => 'yyyy-MM-dd hh:mm:ss', 'locale' => 'en', 'timezone' => 'Europe/Paris'))
            ->add('shop');
    }

    /*public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);
        $query->andWhere(
            $query->expr()->eq($query->getRootAlias().'.status',
                ':status')
        );
        $query->setParameter('status', '1');
        return $query;
    }*/

    protected $datagridValues = array(
        '_page' => 1,
        '_per_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'created',
        'status' => array(
            'type' => EqualType::TYPE_IS_EQUAL,
            'value' => BooleanType::TYPE_YES,
        ),
    );

       public function prePersist($object)
    {
        $uniqid = $this->getRequest()->query->get('uniqid');
        $formData = $this->getRequest()->request->get($uniqid);
        $object->setAdministrator($formData['administrator']);
     
    }

    public function preUpdate($object)
    {
        $uniqid = $this->getRequest()->query->get('uniqid');
        $formData = $this->getRequest()->request->get($uniqid);
        $object->setAdministrator($formData['administrator']);
     
    }

}
