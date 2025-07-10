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

namespace Mds\PimPrint\CoreBundle\Project\Traits;

use Mds\PimPrint\CoreBundle\InDesign\CommandQueue;
use Mds\PimPrint\CoreBundle\Project\AbstractProject;
use Mds\PimPrint\CoreBundle\Service\AccessorTraits\ProjectsManagerTrait;

/**
 * Trait ProjectAwareTrait
 *
 * @package Mds\PimPrint\CoreBundle\Project\Traits
 */
trait ProjectAwareTrait
{
    use ProjectsManagerTrait;

    /**
     * Returns currently generated project.
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
     * @throws \Exception
     */
    protected function getCommandQueue(): CommandQueue
    {
        return $this->getProject()
                    ->getCommandQueue();
    }
}
