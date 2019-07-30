<?php

namespace iFlair\LetsBonusFrontBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Yaml\Parser;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class iFlairLetsBonusFrontExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        /*$yamlParser = new Parser();
        $conf = $yamlParser->parse(file_get_contents(__DIR__.'/../../../../app/config/config.yml'));

        $container->setParameter('hwi_oauth.facebook_front.type', $conf['hwi_oauth']['resource_owners']['facebook_front']['type']);
        $container->setParameter('hwi_oauth.facebook_front.client_id', $conf['hwi_oauth']['resource_owners']['facebook_front']['client_id']);
        $container->setParameter('hwi_oauth.facebook_front.client_secret', $conf['hwi_oauth']['resource_owners']['facebook_front']['client_secret']);
        $container->setParameter('hwi_oauth.facebook_front.scope', $conf['hwi_oauth']['resource_owners']['facebook_front']['scope']);

        $container->setParameter('hwi_oauth.google_front.type', $conf['hwi_oauth']['resource_owners']['google_front']['type']);
        $container->setParameter('hwi_oauth.google_front.client_id', $conf['hwi_oauth']['resource_owners']['google_front']['client_id']);
        $container->setParameter('hwi_oauth.google_front.client_secret', $conf['hwi_oauth']['resource_owners']['google_front']['client_secret']);
        $container->setParameter('hwi_oauth.google_front.scope', $conf['hwi_oauth']['resource_owners']['google_front']['scope']);*/

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
