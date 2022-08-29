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

namespace Mds\PimPrint\CoreBundle\InDesign\CustomField;

use Mds\PimPrint\CoreBundle\InDesign\CustomField\Traits\SelectOptionsTrait;

/**
 * Class Select
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\CustomField
 */
class Select extends AbstractField
{
    use SelectOptionsTrait;

    /**
     * Form field type constant
     *
     * @var string
     */
    const TYPE = 'select';

    /**
     * Values in select field
     *
     * @var array
     */
    private $values = [];

    /**
     * Sets values to show in InDesign select field.
     *
     * @param array $values
     *
     * @return Select
     * @throws \Exception
     */
    final public function setValues(array $values): Select
    {
        $this->values = [];
        foreach ($values as $value) {
            $this->addValue($value);
        }

        return $this;
    }

    /**
     * Sets values to show in InDesign select field without validating format for value, label keys.
     * Use only when you really know what you do!
     *
     * @param array $values
     *
     * @return Select
     */
    final public function setValuesRaw(array $values): Select
    {
        $this->values = $values;

        return $this;
    }

    /**
     * Adds $value to values array to show in InDesign select field
     *
     * @param array $value
     *
     * @return Select
     * @throws \Exception
     */
    final public function addValue(array $value): Select
    {
        if (!isset($value['label'])) {
            throw new \Exception("Value for select field must have a 'label' key");
        }
        if (!isset($value['value'])) {
            throw new \Exception("Value for select field must have a 'value' key");
        }
        $this->values[] = $value;

        return $this;
    }

    /**
     * Adds $value / $label value to values array
     *
     * @param string $value
     * @param string $label
     *
     * @return Select
     * @throws \Exception
     */
    final public function addValueRaw(string $value, string $label): Select
    {
        $this->addValue(
            [
                'value' => $value,
                'label' => $label
            ]
        );

        return $this;
    }

    /**
     * Clears all select values
     *
     * @return Select
     */
    final public function clearValues(): Select
    {
        $this->values = [];

        return $this;
    }

    /**
     * Returns values for select field in InDesign Plugin
     *
     * @return array
     */
    final public function getValues(): array
    {
        return $this->values;
    }

    /**
     * Builds InDesign configuration array
     *
     * @return array
     * @throws \Exception
     */
    public function buildPluginConfig(): array
    {
        $return = parent::buildPluginConfig();

        $return['multiple'] = $this->multiple;
        $return['collapseAmount'] = $this->collapseAmount;
        $return['values'] = $this->values;

        return $return;
    }
}
