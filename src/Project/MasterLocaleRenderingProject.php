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

namespace Mds\PimPrint\CoreBundle\Project;

use League\Flysystem\FilesystemException;
use Mds\PimPrint\CoreBundle\Service\PluginParameters;

/**
 * Class MasterLocaleRenderingProject
 *
 * @package Mds\PimPrint\CoreBundle\Project
 */
abstract class MasterLocaleRenderingProject extends AbstractProject
{
    /**
     * Default update modes, when no project-specific config is defined.
     *
     * @var array
     */
    protected array $defaultUpdateModes = [
        PluginParameters::RENDER_MODE_POSITION_CONTENT,
        PluginParameters::RENDER_MODE_CONTENT,
        PluginParameters::RENDER_MODE_SELECTED_CONTENT,
        PluginParameters::RENDER_MODE_LOCALIZED_POSITION_CONTENT,
        PluginParameters::RENDER_MODE_LOCALIZED_CONTENT,
//        PluginParameters::RENDER_MODE_LOCALIZED_POSITIONS,
        PluginParameters::RENDER_MODE_SELECTED_LOCALIZED_CONTENT,
//        PluginParameters::RENDER_MODE_SELECTED_LOCALIZED_POSITIONS,
    ];

    /**
     * Allowed update modes.
     *
     * @var array
     */
    protected array $allowedUpdateModes = [
        PluginParameters::RENDER_MODE_POSITION_CONTENT,
        PluginParameters::RENDER_MODE_CONTENT,
        PluginParameters::RENDER_MODE_SELECTED_CONTENT,
        PluginParameters::RENDER_MODE_LOCALIZED_POSITION_CONTENT,
        PluginParameters::RENDER_MODE_LOCALIZED_CONTENT,
//        PluginParameters::RENDER_MODE_LOCALIZED_POSITIONS,
        PluginParameters::RENDER_MODE_SELECTED_LOCALIZED_CONTENT,
//        PluginParameters::RENDER_MODE_SELECTED_LOCALIZED_POSITIONS,
    ];

    /**
     * {@inheritDoc}
     *
     * @return array
     * @throws FilesystemException
     * @throws \Exception
     */
    final public function getSettings(): array
    {
        $return = parent::getSettings();
        $return['isLocalized'] = true;

        return $return;
    }
}
