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

use Mds\PimPrint\CoreBundle\Service\ProjectsManager;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Class MdsPimPrintCoreExtension
 *
 * @package Mds\PimPrint\CoreBundle\DependencyInjection
 */
class MdsPimPrintCoreExtension extends Extension
{
    /**
     * {@inheritDoc}
     *
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('aliases.yml');

        $this->registerProjects($container, $config);
    }

    /**
     * Registers projects from configuration in Projects service.
     *
     * @param ContainerBuilder $container
     * @param array            $config
     */
    public function registerProjects(ContainerBuilder $container, array $config)
    {
        if (false === isset($config['projects'])) {
            return;
        }
        $arguments = $config['projects'];
        unset($config['projects']);
        foreach ($arguments as &$argument) {
            $argument = array_merge($argument, $config);
        }
        $definition = $container->getDefinition(ProjectsManager::class);
        $definition->setArgument('$config', $arguments);
    }
}
