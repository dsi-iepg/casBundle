<?php

namespace Iepg\Bundle\Cas\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('cas_connection');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('cas_host')->isRequired()->end()
                ->scalarNode('cas_path')->defaultValue('')->end()
                ->integerNode('cas_port')->defaultValue(443)->end()
                ->booleanNode('cas_ca')->defaultValue(false)->end()
                ->scalarNode('cas_ca_path')->defaultValue('')->end()
                ->scalarNode('cas_dispatcher_name')->defaultValue('cas_dispatcher')->end()
                ->scalarNode('cas_user_unknown')->defaultValue('')->end()
            ->end();

        return $treeBuilder;
    }
}