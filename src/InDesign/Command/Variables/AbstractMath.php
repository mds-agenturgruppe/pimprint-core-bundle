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

namespace Mds\PimPrint\CoreBundle\InDesign\Command\Variables;

use Mds\PimPrint\CoreBundle\InDesign\Command\ExecuteScript;

/**
 * Class AbstractMath
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command\Variables
 */
abstract class AbstractMath extends ExecuteScript implements DependentInterface
{
    /**
     * Used math operation.
     *
     * @var string
     */
    const MATH_OPERATION = '';

    /**
     * Variable name to set.
     *
     * @var string
     */
    protected $name;

    /**
     * Variables to use for math operation.
     *
     * @var string[]
     */
    protected $variables = [];

    /**
     * AbstractMath constructor.
     *
     * @param string $name
     * @param array  $variables
     *
     * @throws \Exception
     */
    public function __construct($name, array $variables = [])
    {
        parent::__construct('');

        $this->setName($name);
        $this->setVariables($variables);
    }

    /**
     * Sets name of variable in InDesign.
     *
     * @param string $name Name of variable in InDesign.
     *
     * @return AbstractMath
     * @throws \Exception
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Returns name of InDesign target variable.
     *
     * @return string
     * @throws \Exception
     */
    public function getName()
    {
        if (null === $this->name) {
            throw new \Exception('No target InDesign variable name defined.');
        }

        return $this->name;
    }

    /**
     * Sets variable names to use to math calculation.
     * Throws an exception if a variable isn't defined in InDesign.
     *
     * @param array $variables
     *
     * @return AbstractMath
     */
    public function setVariables(array $variables)
    {
        foreach ($variables as $variable) {
            $this->addVariable($variable);
        }

        return $this;
    }

    /**
     * Adds $variable to variables.
     * Throws an exception if $variable isn't defined in InDesign.
     *
     * @param string $variable
     *
     * @return AbstractMath|void
     */
    public function addVariable(string $variable)
    {
        if (true === $this->hasVariable($variable)) {
            return $this;
        }

        $this->variables[$variable] = true;

        return $this;
    }

    /**
     * Returns true of $variable is set in variables. Otherwise false is returned.
     *
     * @param string $variable
     *
     * @return bool
     */
    public function hasVariable(string $variable)
    {
        return isset($this->variables[$variable]);
    }

    /**
     * Returns all set variables.
     *
     * @return array
     */
    public function getVariables()
    {
        return array_keys($this->variables);
    }

    /**
     * {@inheritDoc}
     *
     * @return array
     */
    public function getDependentVariables(): array
    {
        return $this->getVariables();
    }

    /**
     * {@inheritDoc}
     *
     * @param bool $addCmd
     *
     * @return array
     * @throws \Exception
     */
    public function buildCommand(bool $addCmd = true)
    {
        $script = 'PimPrintDocument.setVar("' . $this->getName() . '",Math.'.static::MATH_OPERATION.'(';
        foreach ($this->getVariables() as $variable) {
            $script .= 'PimPrintDocument.getVar("' . $variable. '"),';
        }
        $script .= '));';
        $this->setScript($script);

        return parent::buildCommand($addCmd);
    }
}
