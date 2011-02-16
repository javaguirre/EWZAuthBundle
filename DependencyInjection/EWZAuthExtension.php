<?php

namespace EWZ\AuthBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

class EWZAuthExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        foreach ($configs as $config) {
            $this->doConfigLoad($config, $container);
        }
    }

    /**
     * Loads the auth configuration.
     *
     * @param array            $config    An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    protected function doConfigLoad(array $config, ContainerBuilder $container)
    {
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
     * @param array            $config    A configuration array
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    protected function registerFacebookConfiguration($config, ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ewz.auth.facebook')) {
            $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
            $loader->load('facebook.xml');
        }

        foreach (array('class', 'file', 'app_id', 'secret', 'cookie') as $attribute) {
            if (isset($config[$attribute])) {
                $container->setParameter('auth.facebook.'.$attribute, $config[$attribute]);
            }
        }
    }

    /**
     * Loads the twitter configuration.
     *
     * @param array            $config    A configuration array
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    protected function registerTwitterConfiguration($config, ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ewz.auth.twitter')) {
            $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
            $loader->load('twitter.xml');
        }

        if (isset($config['api']['class'])) {
            $container->setParameter('auth.twitter.api.class', $config['api']['class']);
        }

        foreach (array('key', 'secret') as $attribute) {
            if (isset($config[$attribute])) {
                $container->setParameter('auth.twitter.'.$attribute, $config[$attribute]);
            }
        }
    }

    /**
     * Returns the base path for the XSD files.
     *
     * @return string The XSD base path
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__.'/../Resources/config/schema';
    }

    /**
     * Returns the namespace to be used for this extension (XML namespace).
     *
     * @return string The XML namespace
     */
    public function getNamespace()
    {
        return 'http://www.symfony-project.org/schema/dic/ewz/auth';
    }

    /**
     * Returns the recommended alias to use in XML.
     *
     * This alias is also the mandatory prefix to use when using YAML.
     *
     * @return string The alias
     */
    public function getAlias()
    {
        return 'ewz_auth';
    }
}
