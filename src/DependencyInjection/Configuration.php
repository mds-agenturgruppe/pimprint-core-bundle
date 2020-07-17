<?php
/**
 * mds PimPrint
 *
 * This source file is licensed under GNU General Public License version 3 (GPLv3).
 *
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) mds. Agenturgruppe GmbH (https://www.mds.eu)
 * @license    https://pimprint.mds.eu/license GPLv3
 */

namespace Mds\PimPrint\CoreBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @package Mds\PimPrint\CoreBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('mds_pim_print_core');
        $this->addProjectConfig($rootNode);

        return $treeBuilder;
    }

    /**
     * Add PimPrint projects configuration.
     *
     * @param ArrayNodeDefinition $rootNode
     */
    private function addProjectConfig(ArrayNodeDefinition $rootNode)
    {
        $rootNode->children()
                    ->arrayNode('projects')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('ident')
                                ->info('Optional identifier. If omitted the config key in projects is used.')->end()
                            ->scalarNode('name')->isRequired()
                                ->info('Name of project displayed in InDesign-Plugin.')->end()
                            ->scalarNode('service')->isRequired()
                                ->info('Service to use to render the project. Must inherit AbstractProject')->end()
                            ->arrayNode('template')->isRequired()
                                ->info('InDesign Template settings')
                                ->children()
                                    ->scalarNode('default')
                                        ->info('Optional InDesign template filename.')->end()
                                    ->booleanNode('download')->defaultValue(false)
                                        ->info('Download template')->end()
                                ->end()
                            ->end()
                            ->arrayNode('assets')->isRequired()
                                ->info('Asset handling settings')
                                ->children()
                                    ->booleanNode('download')->defaultValue(true)
                                        ->info('Download assets')->end()
                                    ->booleanNode('preDownload')->defaultValue(false)
                                        ->info('Download assets')->end()
                                    ->booleanNode('warningsOnPage')->defaultValue(true)
                                        ->info('Missing asset warnings are displayed as onPage messages.')->end()
                                    ->scalarNode('thumbnail')
                                        ->info('Optional Pimcore thumbnail configuration for preview images.')->end()
                                ->end()
                            ->end()
                            ->arrayNode('host')
                                    ->info('Optional host settings for proxy or multi domain environments.')
                                ->children()
                                    ->scalarNode('hostname')
                                        ->info('Hostname')->end()
                                    ->scalarNode('protocol')
                                        ->info('Protocol')->end()
                                    ->scalarNode('port')
                                        ->info('Port')->end()
                                ->end()
                            ->end()
                            ->scalarNode('php_time_limit')
                                ->info('Optional PHP setting time_limit for project generation.')->end()
                            ->scalarNode('php_memory_limit')
                                    ->info('Optional PHP setting memory_limit for project generation.')->end()
                        ->end()
                    ->end()
                 ->end();
    }
}
