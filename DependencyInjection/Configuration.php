<?php

namespace EWZ\Bundle\AuthBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ewz_auth');

        $this->addFacebookSection($rootNode);
        $this->addTwitterSection($rootNode);

        return $treeBuilder;
    }

    /**
     * Configures the "facebook" section
     */
    private function addFacebookSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('facebook')
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('class')->defaultValue('Facebook')->end()
                        ->scalarNode('file')->defaultValue('%kernel.root_dir%/../vendor/facebook/php-sdk/src/facebook.php')->end()
                        ->scalarNode('app_id')->defaultNull()->end()
                        ->scalarNode('secret')->defaultNull()->end()
                        ->scalarNode('cookie')->defaultTrue()->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * Configures the "twitter" section
     */
    private function addTwitterSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('twitter')
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('class')->defaultValue('TwitterOAuth\Api')->end()
                        ->scalarNode('key')->defaultNull()->end()
                        ->scalarNode('secret')->defaultNull()->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
