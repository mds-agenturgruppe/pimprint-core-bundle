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

namespace Mds\PimPrint\CoreBundle\Service;

use Mds\PimPrint\CoreBundle\Project\AbstractProject;
use Mds\PimPrint\CoreBundle\Project\Config;
use Mds\PimPrint\CoreBundle\Project\MasterLocaleRenderingProject;

/**
 * ProjectsManager registers all configured PimPrint rendering project services defined in
 * mds_pim_print_core configuration and acts as a factory for accessing the concrete rendering services.
 *
 * @package Mds\PimPrint\CoreBundle\Service
 */
class ProjectsManager
{
    /**
     * Configuration of registered PimPrint Projects.
     *
     * @var array
     */
    private array $projects = [];

    /**
     * Instance of current selected project for generation.
     *
     * @var AbstractProject
     */
    private AbstractProject $project;

    /**
     * Projects constructor.
     *
     * @param array $config
     *
     * @throws \Exception
     */
    public function __construct(array $config)
    {
        $this->registerProjects($config);
    }

    /**
     * Registers all PimPrint projects from $config.
     *
     * @param array $config
     *
     * @return void
     */
    private function registerProjects(array $config): void
    {
        foreach ($config as $ident => $project) {
            if (empty($project['ident'])) {
                $project['ident'] = (string)$ident;
            } else {
                $ident = $project['ident'];
            }
            $this->projects[$ident] = $project;
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
        foreach ($this->projects as $ident => $project) {
            $return[] = $this->projectServiceFactory($ident, false)
                             ->getInfo();
        }

        return $return;
    }

    /**
     * Returns current selected project.
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
     * Returns true if current rendered project is a LocalizedRenderingProject
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
        if (false === isset($this->projects[$ident])) {
            throw new \Exception(
                sprintf("No PimPrint project with ident '%s' registered.", $ident)
            );
        }
        $config = $this->projects[$ident];
        try {
            $service = \Pimcore::getKernel()
                               ->getContainer()
                               ->get($config['service']);
        } catch (\Exception) {
            throw new \Exception(
                sprintf("No public PimPrint project service '%s' found.", $config['service'])
            );
        }
        if (false === $service instanceof AbstractProject) {
            throw new \Exception(
                sprintf(
                    "PimPrint project service '%s' is no instance of '%s'.",
                    $ident,
                    AbstractProject::class
                )
            );
        }
        $service->setConfig(new Config($config));
        $service->assertServiceInitialized();
        if (true === $registerSelected) {
            $this->project = $service;
        }

        return $service;
    }
}
