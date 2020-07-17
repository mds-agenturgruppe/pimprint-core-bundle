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

/**
 * Class AbstractCommand
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command
 */
abstract class AbstractCommand
{
    /**
     * Available command params with default values.
     *
     * @var array
     */
    private $availableParams = [];

    /**
     * Params for command.
     *
     * @var array
     */
    protected $params = [];

    /**
     * Component commands.
     *
     * @var ComponentInterface[]
     */
    private $components = [];

    /**
     * Inits $this->params with $params.
     * Used to initialize availible concrete command params with default values.
     *
     * @param array $params
     *
     * @return AbstractCommand
     */
    protected function initParams(array $params)
    {
        $this->params = array_merge($this->params, $params);

        return $this;
    }

    /**
     * Method is used internaly to remove empty params before generating the InDesign command.
     *
     * @param string $param
     */
    protected function removeParam($param)
    {
        if (isset($this->params[$param])) {
            unset($this->params[$param]);
        }
    }

    /**
     * Returns $param parameter value.
     *
     * @param string $param
     *
     * @return mixed
     * @throws \Exception
     */
    public function getParam($param)
    {
        if (false === array_key_exists($param, $this->params)) {
            throw new \Exception(
                sprintf("Parameter '%s' not defined in command '%s'.", $param, static::class)
            );
        }

        return $this->params[$param];
    }

    /**
     * Sets $value for $param.
     *
     * @param string $param
     * @param mixed  $value
     *
     * @return AbstractCommand
     * @throws \Exception
     */
    protected function setParam($param, $value)
    {
        $method = 'validate' . ucfirst($param);
        if (method_exists($this, $method)) {
            $this->$method($value);
        }
        $this->params[$param] = $value;

        return $this;
    }

    /**
     * Adds $command as a component command.
     *
     * @param ComponentInterface $command
     *
     * @return AbstractCommand
     */
    public function addComponent(ComponentInterface $command)
    {
        $ident = $command->getComponentIdent();
        if (true === $command->isMultipleComponent()) {
            if (false === isset($this->components[$ident])) {
                $this->components[$ident] = [];
            }
            $this->components[$ident][] = $command;

            return $this;
        }
        $this->components[$ident] = $command;

        return $this;
    }

    /**
     * Returns all components in a flat array.
     *
     * @return array
     */
    public function getComponents(): array
    {
        $return = [];
        foreach ($this->components as $commands) {
            if (false === is_array($commands)) {
                $commands = [$commands];
            }
            $return = array_merge($return, $commands);
        }

        return $return;
    }

    /**
     * Template method to validate all params when creating the command that is sent to InDesign.
     * If params are invalid method should throw an Exception.
     *
     * @throws \Exception
     */
    protected function validate()
    {
    }

    /**
     * If $param isn't set or empty() a exception is thrown.
     *
     * @param string      $param
     * @param string|null $method
     *
     * @return void
     * @throws \Exception
     */
    protected function validateEmptyParam(string $param, string $method = null): void
    {
        $value = $this->getParam($param);
        if (empty($value)) {
            $message = sprintf("Parameter '%s' not set for command '%s'.", $param, static::class);
            if (null !== $method) {
                $message .= sprintf(" Use method '%s' to set a parameter value.", $method);
            }
            throw new \Exception($message);
        }
    }

    /**
     * Builds command array that is sent as JSON to InDesign.
     *
     * @param bool $addCmd
     *
     * @return array
     * @throws \Exception
     */
    public function buildCommand(bool $addCmd = true)
    {
        $this->validate();

        $command = [];
        if ($addCmd) {
            $command['cmd'] = static::CMD;
        }
        foreach ($this->params as $param => $value) {
            if (null === $value) {
                continue;
            }
            $command[$param] = $value;
        }

        $this->buildComponents($command);

        return $command;
    }

    /**
     * Adds the command-arrays for components
     *
     * @param array $command
     *
     * @throws \Exception
     */
    protected function buildComponents(array &$command)
    {
        foreach ($this->components as $ident => $commands) {
            if (true === is_array($commands)) {
                $component = [];
                foreach ($commands as $componentCommand) {
                    /* @var $componentCommand AbstractCommand */
                    $component[] = $componentCommand->buildCommand(false);
                }
            } else {
                /* @var $commands AbstractCommand */
                $component = $commands->buildCommand(false);
            }
            $command[$ident] = $component;
        }
    }
}
