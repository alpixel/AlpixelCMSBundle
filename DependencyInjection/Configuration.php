<?php

namespace Alpixel\Bundle\CMSBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('alpixel_cms');

        $rootNode
            ->children()
                ->arrayNode('content_types')
                    ->useAttributeAsKey('name')
                    ->prototype('variable')
                ->end()
            ->end()
            ->scalarNode('exception_template')
                ->defaultValue('page/errors.html.twig')
                ->cannotBeEmpty()
            ->end()
            ->arrayNode('blocks')
                ->useAttributeAsKey('name')
                ->prototype('variable')
            ->end();

        return $treeBuilder;
    }
}
