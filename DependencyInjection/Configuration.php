<?php

namespace EWZ\Bundle\AuthBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * configuration structure.
 */
class Configuration
{
    /**
     * Generates the configuration tree.
     *
     * @return \Symfony\Component\Config\Definition\ArrayNode The config tree
     */
    public function getConfigTree()
    {
        $tree = new TreeBuilder();
        $node = $tree->root('ewz_auth');

        $this->addFacebookSection($node);
        $this->addTwitterSection($node);

        return $tree->buildTree();
    }

    /**
     * Configures the "facebook" section
     */
    private function addFacebookSection(ArrayNodeDefinition $node)
    {
        $node
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
    private function addTwitterSection(ArrayNodeDefinition $node)
    {
        $node
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
