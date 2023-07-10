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

use Mds\PimPrint\CoreBundle\InDesign\CustomField\AbstractField;
use Mds\PimPrint\CoreBundle\Project\AbstractProject;

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
    protected array $factoryFields = [
        'start_alignment' => 'startAlignment',
        'page_bounds'     => 'pageBounds',
        'update_mode'     => 'updateMode',
        'publications'    => 'publications',
    ];

    /**
     * Registered custom InDesign plugin fields.
     *
     * @var AbstractField[]
     */
    private array $customFormFields;

    /**
     * Returns form fields configuration array for InDesign plugin.
     *
     * @return array
     * @throws \Exception
     */
    final public function getFormFieldsConfig(): array
    {
        return [
            'factory' => $this->getFactoryFormFieldsConfig(),
            'custom'  => $this->getCustomFormFieldsConfig(),
        ];
    }

    /**
     * Returns configuration array for factory plugin fields.
     *
     * @return array
     * @throws \Exception
     */
    final protected function getFactoryFormFieldsConfig(): array
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
        if (empty($config['update_modes'])) {
            $return['updateModes'] = $this->defaultUpdateModes;
        } else {
            foreach ($this->allowedUpdateModes as $mode) {
                if (in_array($mode, $config['update_modes'])) {
                    $return['updateModes'][] = $mode;
                }
            }
        }

        if (!$return['publications']['show']) {
            $return['publications']['required'] = false;
        }

        return $return;
    }


    /**
     * Template method to be extended in concrete rendering projects to create custom form fields in InDesign plugin.
     *
     * @return void
     */
    protected function initCustomFormFields(): void
    {
    }

    /**
     * Adds custom form $field to project.
     * If already a fields with the same param name is registered an exception is thrown
     *
     * @param AbstractField $field
     *
     * @return AbstractProject
     * @throws \Exception
     */
    final protected function addCustomFormField(AbstractField $field): AbstractProject
    {
        $param = $field->getParam();
        if (isset($this->customFormFields[$param])) {
            throw new \Exception(
                'Can not add custom field to project. There is already a field defined with param name: ' . $param
            );
        }

        $field->setProject($this);
        $this->customFormFields[$param] = $field;

        return $this;
    }

    /**
     * Returns project specific custom form fields configuration array.
     *
     * @return array
     * @throws \Exception
     */
    final protected function getCustomFormFieldsConfig(): array
    {
        $return = [];
        foreach ($this->getCustomFormFields() as $field) {
            $return[] = $field->buildPluginConfig();
        }

        return $return;
    }

    /**
     * Returns an array with all defined custom InDesign form fields.
     *
     * @return AbstractField[]
     */
    final public function getCustomFormFields(): array
    {
        if (!isset($this->customFormFields)) {
            $this->customFormFields = [];
            $this->initCustomFormFields();
        }

        return $this->customFormFields;
    }

    /**
     * Returns custom InDesign form field identified by $param parameter name.
     * If no field with $param parameter name is registered an exception is thrown.
     *
     * @param string $param
     *
     * @return AbstractField
     * @throws \Exception
     */
    final public function getCustomFormField(string $param): AbstractField
    {
        $fields = $this->getCustomFormFields();
        if (isset($fields[$param]) && $fields[$param] instanceof AbstractField) {
            return $fields[$param];
        }

        throw new \Exception('No custom InDesign form field defined for name: ' . $param);
    }
}
