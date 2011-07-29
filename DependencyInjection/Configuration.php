<?php

namespace EWZ\Bundle\AuthBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class contains the configuration information for the bundle
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ewz_auth');
        
        $this->addFacebookSection($node);
        $this->addTwitterSection($node);

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
                        ->scalarNode('class')->end()
                        ->scalarNode('file')->end()
                        ->scalarNode('app_id')->end()
                        ->scalarNode('secret')->end()
                        ->scalarNode('cookie')->end()
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
                        ->scalarNode('key')->end()
                        ->scalarNode('secret')->end()
                        ->arrayNode('api')
                            ->canBeUnset()
                            ->children()
                                ->scalarNode('class')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
