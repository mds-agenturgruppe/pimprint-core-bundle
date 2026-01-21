<?php
/**
 * mds. Agenturgruppe GmbH
 *
 * This source file is available under the terms of the
 * mds. Commercial License (MCL)
 *
 * Full copyright and license information is available in
 * LICENSE.md, which is distributed with this source code.
 *
 * @copyright Copyright (c) mds. Agenturgruppe GmbH (https://www.mds.eu)
 * @license   mds. Commercial License (MCL)
 */

namespace Mds\PimPrint\CoreBundle\Service;

use Mds\PimPrint\CoreBundle\Project\AbstractProject;
use Mds\PimPrint\CoreBundle\Project\Config;
use Mds\PimPrint\CoreBundle\Project\MasterLocaleRenderingProject;
use Symfony\Component\DependencyInjection\Attribute\TaggedLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * Class ProjectsManager
 *
 * Registers all configured PimPrint rendering project services defined in
 * `mds_pim_print_core` configuration and acts as a factory for accessing the concrete rendering services.
 *
 *
 * @package Mds\PimPrint\CoreBundle\Service
 */
class ProjectsManager
{
    /**
     * Service tag for auto-registered rendering projects services.
     *
     * @var string
     */
    const SERVICE_TAG = 'mds.pimprint.rendering.project';

    /**
     * Configured PimPrint Projects.
     *
     * @var array
     */
    private array $config = [];

    /**
     * ProjectsManager
     *
     * @param ServiceLocator $locator
     * @param array          $config
     */
    public function __construct(
        #[TaggedLocator(self::SERVICE_TAG)] protected ServiceLocator $locator,
        array $config
    ) {
        $this->setConfig($config);
    }

    /**
     * Instance of the current selected project for generation.
     *
     * @var AbstractProject
     */
    private AbstractProject $project;

    /**
     * Registers all PimPrint projects from $config.
     *
     * @param array $config
     *
     * @return void
     */
    private function setConfig(array $config): void
    {
        foreach ($config as $ident => $project) {
            if (empty($project['ident'])) {
                $project['ident'] = (string)$ident;
            } else {
                $ident = $project['ident'];
            }
            $this->config[$ident] = $project;
        }
    }

    /**
     * Returns an array with information for all projects.
     *
     * @return array
     * @throws \Exception
     */
    public function getProjectsInfo(): array
    {
        $return = [];
        foreach ($this->config as $ident => $project) {
            $return[] = $this->projectServiceFactory($ident, false)
                             ->getInfo();
        }

        return $return;
    }

    /**
     * Returns the current selected project.
     *
     * @return AbstractProject
     * @throws \Exception
     */
    public function getProject(): AbstractProject
    {
        if (!isset($this->project)) {
            throw new \Exception('No project selected for generation.');
        }

        return $this->project;
    }

    /**
     * Returns true if the current rendered project is a LocalizedRenderingProject
     *
     * @return bool
     * @throws \Exception
     */
    public function isLocalizedProject(): bool
    {
        return $this->getProject() instanceof MasterLocaleRenderingProject;
    }

    /**
     * Loads and returns project service with $ident.
     *
     * @param string $ident
     * @param bool   $registerSelected
     *
     * @return AbstractProject
     * @throws \Exception
     */
    public function projectServiceFactory(string $ident, bool $registerSelected = true): AbstractProject
    {
        if (!isset($this->config[$ident])) {
            throw new \Exception("PimPrint project not registered: $ident");
        }
        $config = $this->config[$ident];

        $project = $this->locator->get($config['service']);
        if (null === $project) {
            throw new \Exception("PimPrint rendering service not found: {$config['service']}");
        }
        if (!$project instanceof AbstractProject) {
            throw new \Exception("PimPrint rendering service must extend:  " . AbstractProject::class);
        }

        $project->setConfig(new Config($config));
        if ($registerSelected) {
            $this->project = $project;
        }

        return $project;
    }
}
