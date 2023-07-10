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

namespace Mds\PimPrint\CoreBundle\Project;

use League\Flysystem\FilesystemException;
use Mds\PimPrint\CoreBundle\Service\PluginParameters;

/**
 * Class MasterLocaleRenderingProject
 *
 * Rendering allows generation of document in a master locale. When rendering additional languages positions and
 * dimensions for elements can be used from the rendered master locale.
 *
 * Class is registered as abstract service in 'src/Resources/config/services.yml' and aliased as
 * 'mds.pimprint.core.master_locale_rendering_project' to be used in concrete service definitions as parent.
 *
 * Example:
 * {code}
 * Mds\PimPrint\DemoBundle\Project\LocalizationDemo\LocalizationProject:
 *   parent: mds.pimprint.core.master_locale_rendering_project
 * {code}
 *
 * @package Mds\PimPrint\CoreBundle\Project
 */
abstract class MasterLocaleRenderingProject extends AbstractProject
{
    /**
     * Default update modes, when no project specific config is defined.
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
