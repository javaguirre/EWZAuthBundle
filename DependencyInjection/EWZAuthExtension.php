<?php

namespace EWZ\Bundle\AuthBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;

class EWZAuthExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('facebook.xml');
        $loader->load('twitter.xml');

        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->process($configuration->getConfigTree(), $configs);

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
        foreach (array('key', 'secret') as $attribute) {
            if (isset($config[$attribute])) {
                $container->setParameter('auth.twitter.'.$attribute, $config[$attribute]);
            }
        }

        if (isset($config['api']['class'])) {
            $container->setParameter('auth.twitter.api.class', $config['api']['class']);
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
        return 'http://symfony.com/schema/dic/ewz/auth';
    }
}
