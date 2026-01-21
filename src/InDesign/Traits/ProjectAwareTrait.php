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
