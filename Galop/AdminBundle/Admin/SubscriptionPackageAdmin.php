<?php

/*declare(strict_types=1);*/
namespace Galop\AdminBundle\Admin;

use AdminBundle\Entity\SubscriptionPackage;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;



class SubscriptionPackageAdmin extends AbstractAdmin
{
    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('id')
            ->add('title')
            ->add('country','doctrine_orm_string', array(), 'choice',
                array('choices' => array('Belgium' => 'belgium', 'Europe Except Belgium' => 'europe'))
            )
            ->add('timeperiod','doctrine_orm_string', array(), 'choice',
                array('choices' => array('1 Month' => '1Month', '3 Month' => '3Month','6 Month' => '6Month','9 Month' => '9Month','1 Year' => '1Year'))
            )
            ->add('packagestatus','doctrine_orm_string', array(), 'choice',
                array('choices' => array('Active' => '0', 'Inactive' => '1'))
            )
            ->add('price')
            ->add('vat');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('id')
            ->add('title')
            ->add('country', 'choice', [
                'editable' => true,
                'class' => 'Vendor\ExampleBundle\Entity\ExampleStatus',
                'choices' => [
                    'belgium' => 'Belgium',
                    'europe'  => 'Europe Except Belgium',
                ],
            ])
            ->add('timeperiod', 'choice', [
                'editable' => true,
                'class' => 'Vendor\ExampleBundle\Entity\ExampleStatus',
                'choices' => [
                    '1Month' => '1 Month',
                    '3Month' => '3 Month',
                    '6Month' => '6 Month',
                    '9Month' => '9 Month',
                    '1Year' =>  '1 Year',
                ],
            ])
            ->add('packagestatus', 'choice', [
                'editable' => true,
                'class' => 'Vendor\ExampleBundle\Entity\ExampleStatus',
                'choices' => [
                    0 => 'Active',
                    1 => 'Inactive',
                ],
            ])
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    { 
       $formMapper
            ->add('title','text', array('label' => 'Title'))
            ->add('description', 'textarea', array('label' => 'Description'))
            ->add('country', ChoiceType::class,[
                'choices' => [
                    'Belgium' => 'belgium',
                    'Europe Except Belgium' => 'europe',
                ],
            ])
            ->add('timeperiod', ChoiceType::class, [
                'choices' => [
                    '1 Month' => '1Month',
                    '3 Month' => '3Month',
                    '6 Month' => '6Month',
                    '9 Month' => '9Month',
                    '1 Year'  => '1Year',
                ],
            ])
            ->add('price','number', array('attr' => array('min' => 0,'precision'=>3)))
            ->add('vat','integer', array('label' => 'Vat %'))
            ->add('totalprice', TextType::class, [
                'label' => 'Total Price',
                'attr'=> [ 'readonly' => true ]
            ])
            ->add('packagestatus', ChoiceType::class, [
                'choices' => [
                    'Active' => '0',
                    'InActive' => '1',
                ],
            ])
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('title')
            ->add('description')
            ->add('country', 'choice', [
                'editable' => true,
                'class' => 'Vendor\ExampleBundle\Entity\ExampleStatus',
                'choices' => [
                    'belgium' => 'Belgium',
                    'europe'  => 'Europe Except Belgium',
                ],
            ])
            ->add('timeperiod', 'choice', [
                'editable' => true,
                'class' => 'Vendor\ExampleBundle\Entity\ExampleStatus',
                'choices' => [
                    '1Month' => '1 Month',
                    '3Month' => '3 Month',
                    '6Month' => '6 Month',
                    '9Month' => '9 Month',
                    '1Year' =>  '1 Year',
                ],
            ])
            ->add('packagestatus', 'choice', [
                'editable' => true,
                'class' => 'Vendor\ExampleBundle\Entity\ExampleStatus',
                'choices' => [
                    0 => 'Active',
                    1 => 'Inactive',
                ],
            ])
            ->add('price')
            ->add('vat','text',array('label' => 'Vat %'))
            ->add('total price');
    }

    public function configure() {
        $this->setTemplate('edit', 'GalopAdminBundle:Subpackage:package.html.twig');
    }
}
