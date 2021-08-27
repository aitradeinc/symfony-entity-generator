<?php

/**
 * Configuration class.
 */

namespace EntityGenerator\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('doctrine_entity_generator');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('tables')->end()
                    ->useAttributeAsKey('table')
                    ->children()
                        ->arrayNode('fields')->end()
                        ->useAttributeAsKey('field')
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}