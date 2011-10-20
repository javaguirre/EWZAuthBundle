<?php

namespace EWZ\Bundle\AuthBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class EWZAuthExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('facebook.xml');
        $loader->load('twitter.xml');

        if (isset($config['facebook'])) {
            $this->registerFacebookConfiguration($config['facebook'], $container);
        }

        if (isset($config['twitter'])) {
            $this->registerTwitterConfiguration($config['twitter'], $container);
        }

    }

    /**
     * Loads the facebook configuration.
     *
     * @param array            $config
     * @param ContainerBuilder $container
     */
    protected function registerFacebookConfiguration($config, ContainerBuilder $container)
    {
        foreach (array('class', 'file', 'app_id', 'secret', 'cookie') as $attribute) {
            if (isset($config[$attribute])) {
                $container->setParameter('ewz_auth.facebook.'.$attribute, $config[$attribute]);
            }
        }
    }

    /**
     * Loads the twitter configuration.
     *
     * @param array            $config
     * @param ContainerBuilder $container
     */
    protected function registerTwitterConfiguration($config, ContainerBuilder $container)
    {
        foreach (array('class', 'key', 'secret') as $attribute) {
            if (isset($config[$attribute])) {
                $container->setParameter('ewz_auth.twitter.'.$attribute, $config[$attribute]);
            }
        }
    }
}
