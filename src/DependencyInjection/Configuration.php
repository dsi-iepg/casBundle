<?php
namespace Iepg\Bundle\Cas\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration implements ConfigurationInterface
{
    function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder('cas_connection');

        $rootNode = $builder->getRootNode();
        $rootNode->children()
            ->scalarNode('cas_host')
            ->isRequired()
            ->end()
            ->scalarNode('cas_path')
            ->isRequired()
            ->end()
            ->scalarNode('cas_port')
            ->defaultValue(443)
            ->end()
            ->booleanNode('cas_ca')
            ->isRequired()
            ->end()
            ->scalarNode('cas_ca_path')
            ->defaultValue(null)
            ->end()

            ->end();

        return $builder;
    }
}