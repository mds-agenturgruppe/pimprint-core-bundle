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

namespace Mds\PimPrint\CoreBundle\Project\Interfaces;

use Mds\PimPrint\CoreBundle\Service\ProjectsManager;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * Interface RenderingProjectInterface
 *
 * @package Mds\PimPrint\CoreBundle\Project
 */
#[AutoconfigureTag(ProjectsManager::SERVICE_TAG)]
interface RenderingProjectInterface
{
    /**
     * Generates InDesign Commands to build the selected publication in InDesign.
     *
     * @return void
     */
    public function buildPublication(): void;
}
