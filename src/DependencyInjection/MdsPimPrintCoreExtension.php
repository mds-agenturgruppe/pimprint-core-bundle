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
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Class MdsPimPrintCoreExtension
 *
 * @package Mds\PimPrint\CoreBundle\DependencyInjection
 */
class MdsPimPrintCoreExtension extends Extension implements PrependExtensionInterface
{
    /**
     * Loads a specific configuration.
     *
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @return void
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('aliases.yml');

        $this->registerProjects($container, $config);
        $this->configurePluginParams($container, $config);
    }

    /**
     * {@inheritDoc}
     *
     * @param ContainerBuilder $container
     *
     * @return void
     */
    public function prepend(ContainerBuilder $container): void
    {
        $config = [
            'pattern'               => '^/pimprint-api',
            'stateless'             => false,
            'provider'              => 'pimcore_admin',
            'user_checker'          => 'Pimcore\Security\User\UserChecker',
            'entry_point'           => 'Mds\PimPrint\CoreBundle\Security\Authenticator\AdminSessionAuthenticator',
            'custom_authenticators' => [
                'Mds\PimPrint\CoreBundle\Security\Authenticator\AdminSessionAuthenticator',
                'Mds\PimPrint\CoreBundle\Security\Authenticator\InDesignAuthenticator',
            ]
        ];

        $container->setParameter('mds.pimprint.core.firewall_settings', $config);
    }

    /**
     * Registers projects from configuration in Projects service.
     *
     * @param ContainerBuilder $container
     * @param array            $config
     *
     * @return void
     * @throws \Exception
     */
    public function registerProjects(ContainerBuilder $container, array $config): void
    {
        if (false === isset($config['projects'])) {
            return;
        }
        $arguments = $config['projects'];
        unset($config['projects']);
        foreach ($arguments as &$argument) {
            $argument = array_merge($argument, $config);
            $argument['bundlePath'] = $this->getBundlePathForService($container, $argument);
        }
        $definition = $container->getDefinition(ProjectsManager::class);
        $definition->setArgument('$config', $arguments);
    }

    /**
     * Returns bundle path for configured project service in $projectConfig.
     *
     * @param ContainerBuilder $container
     * @param array            $projectConfig
     *
     * @return string
     * @throws \Exception
     */
    private function getBundlePathForService(ContainerBuilder $container, array $projectConfig): string
    {
        if (empty($projectConfig['service'])) {
            throw new \Exception(sprintf('No PimPrint project service defined for rendering project.'));
        }
        foreach (array_reverse($container->getParameter('kernel.bundles_metadata')) as $bundle) {
            if (str_starts_with($projectConfig['service'], $bundle['namespace'])) {
                return $bundle['path'];
            }
        }
        throw new \Exception(sprintf('No matching bundle found for service: %s', $projectConfig['service']));
    }

    /**
     * Configures PluginParams service
     *
     * @param ContainerBuilder $container
     * @param array            $config
     *
     * @return void
     */
    private function configurePluginParams(ContainerBuilder $container, array $config): void
    {
        $definition = $container->getDefinition('mds.pimprint.core.plugin_parameters');
        $definition->setArgument('$config', $config);
    }
}
