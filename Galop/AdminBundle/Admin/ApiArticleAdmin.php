<?php

declare(strict_types=1);

namespace Galop\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Galop\AdminBundle\Entity\User;
use Galop\AdminBundle\Entity\ApiArticle;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Sonata\AdminBundle\Route\RouteCollection;

final class ApiArticleAdmin extends AbstractAdmin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create');
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('id')
            ->add('ApiuserId', null, ['label' => 'Client Name'])
            ->add('ArticleID')
            ->add('ArticleTitle')
            ->add('ArticleLink')
            ->add('createdAt')
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('id')
            ->add('ApiuserId', null, ['label' => 'Client Name'])
            ->add('ArticleID', null, ['label' => 'Article ID'])
            ->add('ArticleTitle')
            ->add('ArticleLink','url', [
                'attributes' => ['target' => '_blank'],
                'hide_protocol' => true,
            ])
            ->add('createdAt', null, [
                'format' => 'Y-m-d H:i',
                'timezone' => 'Europe/Vaduz',
                'label' => 'Created'
            ])
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ])
        ;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('clientkey', 'text', array('label' => 'Client Key'))
            ->add('ArticleTitle', 'text', array('label' => 'Article Title'))
            ->add('ArticleLink', 'text', array('label' => 'Article Link'))
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('ArticleID', null, ['label' => 'Article ID'])
            ->add('ArticleTitle')
            ->add('ArticleLink')
            ->add('createdAt', null, [
                'format' => 'Y-m-d H:i',
                'timezone' => 'Europe/Vaduz',
                'label' => 'Created'
            ])
        ;
    }
}
