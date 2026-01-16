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

namespace Mds\PimPrint\CoreBundle\InDesign\Traits;

use Mds\PimPrint\CoreBundle\Project\AbstractProject;
use Mds\PimPrint\CoreBundle\Service\AccessorTraits\ProjectsManagerTrait;
use Mds\PimPrint\CoreBundle\Service\CommandQueue;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Trait ProjectAwareTrait
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Traits
 */
trait ProjectAwareTrait
{
    use ProjectsManagerTrait;

    /**
     * Returns a currently generated project.
     *
     * @return AbstractProject
     * @throws \Exception
     */
    protected function getProject(): AbstractProject
    {
        return $this->getProjectsManager()
                    ->getProject();
    }

    /**
     * Returns CommandQueue.
     *
     * @return CommandQueue
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Exception
     */
    protected function commandQueue(): CommandQueue
    {
        return $this->getProject()
                    ->commandQueue();
    }
}
