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

use Mds\PimPrint\CoreBundle\Service\PluginParameters;

/**
 * Trait FormFieldsTrait
 *
 * @package Mds\PimPrint\CoreBundle\Project\Traits
 */
trait FormFieldsTrait
{
    /**
     * Factory fields in InDesign plugin.
     *
     * @var array
     */
    protected $factoryFields = [
        'start_alignment' => 'startAlignment',
        'page_bounds'     => 'pageBounds',
        'update_mode'     => 'updateMode'
    ];

    /**
     * Allowed update modes.
     *
     * @var array
     */
    protected $allowedUpdateModes = [
        PluginParameters::UPDATE_ALL_POSITION_CONTENT,
        PluginParameters::UPDATE_ALL_CONTENT,
        PluginParameters::UPDATE_SELECTED_POSITION_CONTENT,
        PluginParameters::UPDATE_SELECTED_CONTENT,
    ];

    /**
     * Returns form fields definition array for InDesign plugin.
     *
     * @return array
     */
    public function getFormFields(): array
    {
        return [
            'factory' => $this->getFactoryFormFields(),
            'custom'  => $this->getCustomFormFields(),
        ];
    }

    /**
     * Returns configuration array for factory plugin fields.
     *
     * @return array
     */
    final protected function getFactoryFormFields()
    {
        $return = [
            'updateModes' => [],
        ];
        $config = $this->config()
                       ->offsetGet('plugin_elements');
        foreach ($this->factoryFields as $key => $field) {
            if (isset($config[$key])) {
                $return[$field] = $config[$key];
            } else {
                $return[$field] = false;
            }
        }
        foreach ($this->allowedUpdateModes as $mode) {
            if (true === in_array($mode, $config['update_modes'])) {
                $return['updateModes'][] = $mode;
            }
        }

        return $return;
    }

    /**
     * Returns project specific custom form fields.
     *
     * @return array
     * @todo Project specific custom form fields into plugin.
     */
    protected function getCustomFormFields()
    {
        return [];
    }
}
