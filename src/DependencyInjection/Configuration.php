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
                    ->arrayNode('lc_numeric')
                        ->info("Optional locales to set in render mode to have float values converted to '.-strings'")
                        ->example(['en_US.UTF-8', 'de_DE.UTF-8'])
                        ->prototype('scalar')->end()
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
                    ->arrayNode('projects')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('ident')
                                ->info('Internal project identifier.')->end()
                            ->scalarNode('name')->isRequired()
                                ->info('Name of project displayed in InDesign-Plugin.')->end()
                            ->scalarNode('service')->isRequired()
                                ->info('Service to use to render the project. Must inherit AbstractProject.')->end()
                            ->booleanNode('create_update_info')->defaultValue(true)
                                ->info('Toggles creation of update info layers.')->end()
                            ->arrayNode('template')->isRequired()
                                ->info('InDesign Template settings.')
                                ->children()
                                    ->scalarNode('default')
                                        ->info('Default InDesign template filename.')->end()
                                    ->scalarNode('relative_path')
                                        ->info('Optional relative path inside service bundle to the InDesign template.')
                                        ->defaultValue('/Resources/pimprint/')->end()
                                    ->booleanNode('download')->defaultValue(true)
                                        ->info('Download template')->end()
                                ->end()
                            ->end()
                            ->arrayNode('plugin_elements')->addDefaultsIfNotSet()
                                ->info('Available plugin elements.')
                                ->children()
                                    ->booleanNode('update_mode')->defaultValue(true)
                                        ->info('Field for update modes.')->end()
                                    ->arrayNode('update_modes')
                                        ->info('Available update modes for project.')
                                    ->defaultValue([501, 502, 512])
                                        ->prototype('scalar')->end()
                                    ->end()
                                    ->booleanNode('start_alignment')->defaultValue(false)
                                        ->info('Field for start left/right page.')->end()
                                    ->booleanNode('page_bounds')->defaultValue(false)
                                        ->info('Fields for page start/end.')->end()
                                ->end()
                            ->end()
                            ->arrayNode('assets')->addDefaultsIfNotSet()
                                ->info('Asset handling settings.')
                                ->children()
                                    ->booleanNode('download')->defaultValue(true)
                                        ->info('Toggles asset download.')->end()
                                    ->booleanNode('pre_download')->defaultValue(true)
                                        ->info('Toggles asset download before rendering start.')->end()
                                    ->booleanNode('warnings_on_page')->defaultValue(true)
                                        ->info('Toggles missing asset onPage warnings messages.')->end()
                                    ->scalarNode('thumbnail')
                                        ->info('Optional Pimcore thumbnail configuration for preview images.')->end()
                                ->end()
                            ->end()
                            ->scalarNode('php_time_limit')->defaultValue('0')
                                ->info('Optional PHP setting time_limit for project generation.')->end()
                            ->scalarNode('php_memory_limit')->defaultValue('2G')
                                    ->info('Optional PHP setting memory_limit for project generation.')->end()
                        ->end()
                    ->end()
                 ->end();
    }
}
