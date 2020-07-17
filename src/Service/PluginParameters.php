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

namespace Mds\PimPrint\CoreBundle\Service;

use Pimcore\Http\RequestHelper;

/**
 * Class PluginParameters
 *
 * @package Mds\PimPrint\CoreBundle\Service
 */
class PluginParameters
{
    /**
     * Option value to start on left page of a print bow.
     *
     * @var int
     */
    const OPTION_START_LEFT_PAGE = 400;

    /**
     * Option value to start on right page of a print bow.
     *
     * @var int
     */
    const OPTION_START_RIGHT_PAGE = 401;

    /**
     * Plugin param publication ident.
     *
     * @var string
     */
    const PARAM_PUBLICATION = 'publicationIdent';

    /**
     * Plugin param render option.
     *
     * @var string
     */
    const PARAM_OPTION = 'renderOption';

    /**
     * Plugin param render language.
     *
     * @var string
     */
    const PARAM_LANGUAGE = 'renderLanguage';

    /**
     * Plugin param start page.
     *
     * @var string
     */
    const PARAM_PAGE_START = 'pageStart';

    /**
     * Plugin param end page.
     *
     * @var string
     */
    const PARAM_PAGE_END = 'pageEnd';

    /**
     * Plugin param render mode.
     *
     * @var string
     */
    const PARAM_MODE = 'renderMode';

    /**
     * Plugin param element list.
     *
     * @var string
     */
    const PARAM_ELEMENT_LIST = 'elementList';

    /**
     * Pimcore request helper.
     *
     * @var RequestHelper
     */
    protected $requestHelper;

    /**
     * JsonRequestDecoder instance.
     *
     * @var JsonRequestDecoder
     */
    protected $jsonRequestDecoder;

    /**
     * Parameter definition to load from request.
     * Boolean value indicates if param a required parameter.
     *
     * @var array
     */
    protected $paramDefinition = [
        self::PARAM_PUBLICATION  => ['required' => true, 'default' => null],
        self::PARAM_LANGUAGE     => ['required' => true, 'default' => null],
        self::PARAM_MODE         => ['required' => false, 'default' => null],
        self::PARAM_OPTION       => ['required' => false, 'default' => PluginParameters::OPTION_START_LEFT_PAGE],
        self::PARAM_PAGE_START   => ['required' => false, 'default' => 1],
        self::PARAM_PAGE_END     => ['required' => false, 'default' => false],
        self::PARAM_ELEMENT_LIST => ['required' => false, 'default' => []],
    ];

    /**
     * Loaded params from request.
     *
     * @var array
     */
    protected $params = [];

    /**
     * PluginParams constructor.
     *
     * @param RequestHelper      $requestHelper
     * @param JsonRequestDecoder $jsonRequestDecoder
     */
    public function __construct(RequestHelper $requestHelper, JsonRequestDecoder $jsonRequestDecoder)
    {
        $this->requestHelper = $requestHelper;
        $this->jsonRequestDecoder = $jsonRequestDecoder;
    }

    /**
     * Returns plugin $param. Use class constants to identify param.
     *
     * @param string $param
     *
     * @return mixed|null
     * @throws \Exception
     */
    public function get(string $param)
    {
        if (empty($this->params)) {
            $this->load();
        }
        if (false === isset($this->params[$param])) {
            throw new \Exception(sprintf("Undefined plugin parameter '%s'.", $param));
        }

        return $this->params[$param];
    }

    /**
     * Returns true of PluginParam option is set to start on left page.
     *
     * @return bool
     * @throws \Exception
     */
    public function isStartOnLeftPage()
    {
        return PluginParameters::OPTION_START_LEFT_PAGE == $this->get(PluginParameters::PARAM_OPTION);
    }

    /**
     * Returns true of PluginParam option is set to start on right page.
     *
     * @return bool
     * @throws \Exception
     */
    public function isStartOnRightPage()
    {
        return !$this->isStartOnLeftPage();
    }

    /**
     * Loads params from InDesign plugin from current request.
     * Throws an exception if required params are missing.
     *
     * @throws \Exception
     */
    protected function load()
    {
        $request = $this->requestHelper->getRequest();
        $this->jsonRequestDecoder->decode($request);

        foreach ($this->paramDefinition as $param => $definition) {
            $required = $definition['required'];
            $default = $definition['default'];
            $value = $request->get($param);
            if (true === $required && true === empty($value)) {
                throw new \Exception(sprintf("Required plugin parameter '%s' not found in request.", $param));
            }
            if (true === empty($value)) {
                $value = $default;
            }

            $this->params[$param] = $value;
        }
    }

    /**
     * Returns PimPrint-Plugin options.
     *
     * @return array
     */
    public function getOptions(): array
    {
        return [
            [
                'option' => PluginParameters::OPTION_START_LEFT_PAGE,
                'label'  => 'Start on left page',
            ],
            [
                'option' => PluginParameters::OPTION_START_RIGHT_PAGE,
                'label'  => 'Start on right page',
            ],
        ];
    }
}
