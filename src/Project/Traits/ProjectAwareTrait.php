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

namespace Mds\PimPrint\CoreBundle\Project\Traits;

use Mds\PimPrint\CoreBundle\InDesign\CommandQueue;
use Mds\PimPrint\CoreBundle\Project\AbstractProject;
use Mds\PimPrint\CoreBundle\Service\ProjectsManager;

/**
 * Trait ProjectAwareTrait
 *
 * @package Mds\PimPrint\CoreBundle\Project\Traits
 */
trait ProjectAwareTrait
{
    /**
     * Returns currently generated project.
     *
     * @return AbstractProject
     * @throws \Exception
     */
    protected function getProject(): AbstractProject
    {
        return ProjectsManager::getProject();
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
