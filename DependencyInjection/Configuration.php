<?php

namespace EWZ\AuthBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * configuration structure.
 */
class Configuration
{
    /**
     * Generates the configuration tree.
     *
     * @return \Symfony\Component\Config\Definition\NodeInterface
     */
    public function getConfigTree()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ewz_auth', 'array');

        $this->addFacebookSection($rootNode);
        $this->addTwitterSection($rootNode);

        return $treeBuilder->buildTree();
    }

    private function addFacebookSection(NodeBuilder $rootNode)
    {
        $rootNode
            ->arrayNode('facebook')
                ->canBeUnset()
                ->scalarNode('class')->end()
                ->scalarNode('file')->end()
                ->scalarNode('app_id')->end()
                ->scalarNode('secret')->end()
                ->scalarNode('cookie')->end()
                ->end()
        ;
    }

    private function addTwitterSection(NodeBuilder $rootNode)
    {
        $rootNode
            ->arrayNode('twitter')
                ->canBeUnset()
                ->scalarNode('key')->end()
                ->scalarNode('secret')->end()
                ->arrayNode('api')
                    ->canBeUnset()
                    ->scalarNode('class')->end()
                    ->end()
                ->end()
        ;
    }
}
