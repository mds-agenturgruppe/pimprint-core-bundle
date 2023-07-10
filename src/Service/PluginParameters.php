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
     * Parameter value to start on left page of a print bow.
     *
     * @var int
     */
    const START_ALIGNMENT_LEFT = 400;

    /**
     * Parameter value to start on right page of a print bow.
     *
     * @var int
     */
    const START_ALIGNMENT_RIGHT = 401;

    /**
     * Render mode "Generate"
     * Generates or updates elements to position send from server and sets content sent from server.
     *
     * Available in projects:
     * - RenderingProject
     * - LocalizedRenderingProject (Refers to Master-Language)
     *
     * @var int
     */
    const RENDER_MODE_POSITION_CONTENT = 501;


    /**
     * Render mode "Update"
     * Sets content sent from server in all elements. Leaves element positioning untouched.
     * AbstractBox command fits are executed as sent from server.
     *
     * Available in projects:
     * - RenderingProject
     * - LocalizedRenderingProject (Refers to Master-Language)
     *
     * @var int
     */
    const RENDER_MODE_CONTENT = 502;

    /**
     * Render mode "Update selected"
     * Sets content sent from server into selected elements. Leaves element positioning and dimensions untouched.
     * AbstractBox command fits are executed as sent from server.
     *
     * Available in projects:
     * - RenderingProject
     * - LocalizedRenderingProject (Refers to Master-Language)
     *
     * @var int
     */
    const RENDER_MODE_SELECTED_CONTENT = 512;

    /**
     * Render mode "Generate selected"
     * Generates or updates selected elements to position send from server and sets content sent from server.
     *
     * Available in projects:
     * - RenderingProject
     *
     * Important:
     * This update mode only works in absolute positioned layouts.
     * Layouts using relative position or CheckNewPage commands are not absolutely positioned.
     *
     * @var int
     */
    const RENDER_MODE_SELECTED_POSITION_CONTENT = 511;

    /**
     * Render mode "Generate language variants"
     * Generates or updates elements for language variants to position from the master-language
     * and sets content sent from server.
     *
     * Available in projects:
     * - LocalizedRenderingProject (Refers to language variants)
     *
     * @var int
     */
    const RENDER_MODE_LOCALIZED_POSITION_CONTENT = 521;

    /**
     * Render mode "Update language variants"
     * Sets content sent from server for language variants in all elements. Leaves element positioning and dimensions
     * untouched.
     * AbstractBox command fits are executed as sent from server.
     *
     * Available in projects:
     * - LocalizedRenderingProject (Refers to language variants)
     *
     * @var int
     */
    const RENDER_MODE_LOCALIZED_CONTENT = 522;

    /**
     * Render mode "Generate language variants positions"
     * Updates positions for language variants in all elements to the position from the master-language.
     *
     * Available in projects:
     * - LocalizedRenderingProject (Refers to language variants)
     *
     *  @var int
     */
    const RENDER_MODE_LOCALIZED_POSITIONS = 523;

    /**
     * Render mode "Update selected language variants"
     * Sets content sent from server for language variants in selected elements. Leaves element positioning and
     * dimensions untouched.
     * AbstractBox command fits are executed as sent from server.
     *
     * Available in projects:
     * - LocalizedRenderingProject (Refers to language variants)
     *
     * @var int
     */
    const RENDER_MODE_SELECTED_LOCALIZED_CONTENT = 532;

    /**
     * Render mode "Generate selected language variants positions"
     * Updates positions for language variants in selected elements to the position from the master-language.
     *
     * Available in projects:
     * - LocalizedRenderingProject (Refers to language variants)
     *
     *  @var int
     */
    const RENDER_MODE_SELECTED_LOCALIZED_POSITIONS = 533;


    /**
     * Plugin param publication ident.
     *
     * @var string
     */
    const PARAM_PUBLICATION = 'publicationIdent';

    /**
     * Plugin param render language.
     *
     * @var string
     */
    const PARAM_LANGUAGE = 'renderLanguage';

    /**
     * Plugin param startAlignment.
     *
     * @var string
     */
    const PARAM_START_ALIGNMENT = 'startAlignment';

    /**
     * Plugin param update mode.
     *
     * @var string
     */
    const PARAM_UPDATE_MODE = 'updateMode';

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
     * Plugin param element list.
     *
     * @var string
     */
    const PARAM_ELEMENT_LIST = 'elementList';

    /**
     * Plugin param custom fields
     *
     * @var string
     */
    const PARAM_CUSTOM_FIELDS = 'custom';

    /**
     * Plugin param projectIdent
     *
     * @var string
     */
    const PARAM_PROJECT_IDENT = 'identifier';

    /**
     * Pimcore request helper.
     *
     * @var RequestHelper
     */
    protected RequestHelper $requestHelper;

    /**
     * JsonRequestDecoder instance.
     *
     * @var JsonRequestDecoder
     */
    protected JsonRequestDecoder $jsonRequestDecoder;

    /**
     * Parameter definition to load from request.
     * Boolean value indicates if param a required parameter.
     *
     * @var array
     */
    protected array $paramDefinition = [
        self::PARAM_PUBLICATION     => ['required' => null, 'default' => null],
        self::PARAM_LANGUAGE        => ['required' => true, 'default' => null],
        self::PARAM_UPDATE_MODE     => ['required' => false, 'default' => self::RENDER_MODE_POSITION_CONTENT],
        self::PARAM_START_ALIGNMENT => ['required' => false, 'default' => self::START_ALIGNMENT_LEFT],
        self::PARAM_PAGE_START      => ['required' => false, 'default' => 1],
        self::PARAM_PAGE_END        => ['required' => false, 'default' => false],
        self::PARAM_ELEMENT_LIST    => ['required' => false, 'default' => []],
        self::PARAM_CUSTOM_FIELDS   => ['required' => false, 'default' => []],
    ];

    /**
     * Loaded params from request.
     *
     * @var array
     */
    private array $params = [];

    /**
     * PimPrint configuration
     *
     * @var array
     */
    private array $config;

    /**
     * PluginParams constructor.
     *
     * @param RequestHelper      $requestHelper
     * @param JsonRequestDecoder $jsonRequestDecoder
     * @param array              $config
     */
    public function __construct(RequestHelper $requestHelper, JsonRequestDecoder $jsonRequestDecoder, array $config)
    {
        $this->requestHelper = $requestHelper;
        $this->jsonRequestDecoder = $jsonRequestDecoder;
        $this->config = $config;
    }

    /**
     * Returns plugin $param. Use class constants to identify param.
     *
     * @param string $param
     *
     * @return mixed
     * @throws \Exception
     */
    public function get(string $param): mixed
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
    public function isStartOnLeftPage(): bool
    {
        return PluginParameters::START_ALIGNMENT_LEFT == $this->get(PluginParameters::PARAM_START_ALIGNMENT);
    }

    /**
     * Returns true of PluginParam option is set to start on right page.
     *
     * @return bool
     * @throws \Exception
     */
    public function isStartOnRightPage(): bool
    {
        return !$this->isStartOnLeftPage();
    }

    /**
     * Returns true if current update mode is for selected elements.
     *
     * @return bool
     * @throws \Exception
     */
    public function isUpdateModeSelected(): bool
    {
        $mode = $this->get(self::PARAM_UPDATE_MODE);

        return in_array(
            $mode,
            [
                self::RENDER_MODE_SELECTED_POSITION_CONTENT,
                self::RENDER_MODE_SELECTED_CONTENT,
                self::RENDER_MODE_SELECTED_LOCALIZED_CONTENT,
                self::RENDER_MODE_SELECTED_LOCALIZED_POSITIONS,
            ]
        );
    }

    /**
     * Loads params from InDesign plugin from current request.
     * Throws an exception if required params are missing.
     *
     * @return void
     * @throws \Exception
     */
    protected function load(): void
    {
        $request = $this->requestHelper->getRequest();
        $this->jsonRequestDecoder->decode($request);

        $projectIdent = $request->get(self::PARAM_PROJECT_IDENT);

        $this->paramDefinition[self::PARAM_PUBLICATION]['required'] =
            $this->config['projects'][$projectIdent]['plugin_elements']['publications']['required'];

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
     * Returns run parameters for custom $field.
     * If $additional is true, the optional additionalData value from custom field is returned.
     *
     * @param string $field
     * @param bool   $additional
     *
     * @return string|array|null
     * @throws \Exception
     */
    public function getCustomField(string $field, bool $additional = false): array|string|null
    {
        if ($additional) {
            $field .= 'AdditionalData';
        }
        $custom = $this->get(self::PARAM_CUSTOM_FIELDS);
        if (false === isset($custom[$field])) {
            return null;
        }

        return $custom[$field];
    }
}
