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

namespace Mds\PimPrint\CoreBundle\InDesign\Command;

use Pimcore\Config;

/**
 * Outputs a InDesign variable in Plugin.
 * By default, output is only created in 'dev' environment. Use force option to output in all environments.
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command
 */
class VariableOutput extends ExecuteScript
{
    /**
     * VariableName to output
     *
     * @var string
     */
    private string $variableName;

    /**
     * Optional label of output
     *
     * @var string
     */
    private string $label = '';

    /**
     * Force to output in all environments
     *
     * @var bool
     */
    private bool $force = false;

    /**
     * VariableOutput constructor
     *
     * @param string $variableName Name of variable to output
     * @param string $label        Optional label to output in Plugin. If omitted $variableName is used
     */
    public function __construct(string $variableName, string $label = '')
    {
        $this->setVariableName($variableName);
        $this->setLabel($label);
    }

    /**
     * Sets $variableName to output
     *
     * @param string $variableName
     *
     * @return VariableOutput
     */
    public function setVariableName(string $variableName): VariableOutput
    {
        $this->variableName = $variableName;

        return $this;
    }

    /**
     * Sets optional $label to output in Plugin. If not set $variableName is used
     *
     * @param string $label
     *
     * @return VariableOutput
     */
    public function setLabel(string $label): VariableOutput
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Sets $force to output variable in all environments.
     * By default, output is only created in 'dev' environment.
     *
     * @param bool $force
     *
     * @return VariableOutput
     */
    public function setForce(bool $force = true): VariableOutput
    {
        $this->force = $force;

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @param bool $addCmd
     *
     * @return array
     * @throws \Exception
     */
    public function buildCommand(bool $addCmd = true): array
    {
        if (!$this->force) {
            if ('dev' != Config::getEnvironment()) {
                return [];
            }
        }
        $label = $this->variableName;
        if (!empty($this->label)) {
            $label = $this->label;
        }
        $script = "PimPrintHelper.Messages.notice('" . $label . ": ' + ";
        $script .= "PimPrintHelper.Document.getVar('" . $this->variableName . "'));";

        parent::__construct($script);

        return parent::buildCommand($addCmd);
    }
}
