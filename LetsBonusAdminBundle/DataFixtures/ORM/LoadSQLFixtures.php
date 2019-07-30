<?php

namespace iFlair\LetsBonusAdminBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Finder\Finder;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadSQLFixtures extends LoadContainerFixtures implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function load(ObjectManager $manager)
    {
        // TODO: Implement load() method.
        /*$finder = new Finder();
        $sql_fixure_path = $this->container->getParameter('sql_fixure_path');
        $sql_name = $this->container->getParameter('sql_name');
        $finder->in($sql_fixure_path);
        $finder->name($sql_name);

        foreach ($finder as $file) {
            $content = $file->getContents();
            $letsbonusEm = $this->container->get('doctrine')->getManager('default');
            $letsbonusEm = $this->container->get('doctrine.orm.default_entity_manager');
            $letsbonusEm->getConnection()->exec($content);
            $letsbonusEm->flush();
            $letsbonusEm->clear();
        }*/
    }
}
