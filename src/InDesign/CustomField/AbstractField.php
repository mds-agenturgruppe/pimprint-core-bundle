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

use Mds\PimPrint\CoreBundle\Project\AbstractProject;

/**
 * Class AbstractField
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\CustomField
 */
abstract class AbstractField
{
    /**
     * PimPrint project service the cutsom field is used in.
     *
     * @var AbstractProject
     */
    protected $project;

    /**
     * Param name and ident of custom form field
     *
     * @var string
     */
    private $param;

    /**
     * Label of custom form field in InDesign plugin
     *
     * @var string
     */
    private $label = '';

    /**
     * Custom field is required to start InDesign generation process
     *
     * @var bool
     */
    private $required = false;

    /**
     * Sets $project
     *
     * @param AbstractProject $project
     *
     * @return AbstractField
     */
    public function setProject(AbstractProject $project): AbstractField
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Builds InDesign configuration array
     *
     * @return array
     * @throws \Exception
     */
    public function buildPluginConfig(): array
    {
        return [
            'type'     => static::TYPE,
            'param'    => $this->getParam(),
            'label'    => $this->getLabel(),
            'required' => $this->isRequired(),
        ];
    }

    /**
     * Sets $param name of custom form field in InDesign plugin
     *
     * @param string $param
     *
     * @return AbstractField
     * @throws \Exception
     */
    public function setParam(string $param): AbstractField
    {
        if ('' === $param) {
            throw new \Exception('Custom form field param can not be an empty string');
        }
        $this->param = $param;

        return $this;
    }

    /**
     * Returns param name for custom form field
     *
     * @return string
     * @throws \Exception
     */
    public function getParam(): string
    {
        if (null === $this->param) {
            throw new \Exception('Custom form field must have a param name defined');
        }

        return $this->param;
    }

    /**
     * Sets $label of custom form field in InDesign plugin
     *
     * @param string $label
     *
     * @return AbstractField
     */
    public function setLabel(string $label): AbstractField
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Returns label of custom field in InDesign plugin
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Returns of input in InDesign plugin is required to start generation
     *
     * @return bool
     */
    private function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * Sets $required of input in InDesign plugin to start generation process
     *
     * @param bool $required
     *
     * @return AbstractField
     */
    public function setRequired(bool $required): AbstractField
    {
        $this->required = $required;

        return $this;
    }
}
