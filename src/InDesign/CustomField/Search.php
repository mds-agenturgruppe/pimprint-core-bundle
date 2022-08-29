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
use Mds\PimPrint\CoreBundle\Service\AccessorTraits\UrlGeneratorTrait;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Search
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\CustomField
 */
abstract class Search extends AbstractField
{
    use SelectOptionsTrait;
    use UrlGeneratorTrait;

    /**
     * Form field type constant
     *
     * @var string
     */
    const TYPE = 'search';

    /**
     * Shows an addAllButton to add all search results to the selection in InDesign Plugin
     *
     * @var bool
     */
    private $addAllButton = false;

    /**
     * Add search results automatically to selection in InDesign Plugin
     *
     * @var bool
     */
    private $autoAdd = false;

    /**
     * Clears all selected values upon search execution in InDesign Plugin
     *
     * @var bool
     */
    private $clearValues = false;

    /**
     * Creates Search\Result for search $phrase
     *
     * @param string $phrase
     *
     * @return Search\Result
     */
    abstract public function search(string $phrase): Search\Result;

    /**
     * Sets $addAllButton to show in InDesign Plugin
     *
     * @param bool $addAllButton
     *
     * @return Search
     */
    public function setAddAllButton(bool $addAllButton): Search
    {
        $this->addAllButton = $addAllButton;

        return $this;
    }

    /**
     * Sets $autoAdd to add results automatically to selection in InDesign Plugin
     *
     * @param bool $autoAdd
     *
     * @return Search
     */
    public function setAutoAdd(bool $autoAdd): Search
    {
        $this->autoAdd = $autoAdd;

        return $this;
    }

    /**
     * Sets $clearValues to clear all selected values upon search execution in InDesign Plugin
     *
     * @param bool $clearValues
     *
     * @return Search
     */
    public function setClearValues(bool $clearValues): Search
    {
        $this->clearValues = $clearValues;

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
        $return = parent::buildPluginConfig();

        $return['multiple'] = $this->multiple;
        $return['collapseAmount'] = $this->collapseAmount;
        $return['searchUrl'] = $this->getSearchUrl();
        $return['addAllButton'] = $this->addAllButton;
        $return['autoAdd'] = $this->autoAdd;
        $return['clearValues'] = $this->clearValues;

        return $return;
    }

    /**
     * Executes search and return response attay
     *
     * @param Request $request
     *
     * @return array
     */
    final public function getSearchResponse(Request $request): array
    {
        $phrase = (string)$request->get('search');

        return $this->search($phrase)
                    ->buildResponseData();
    }

    /**
     * Returns search url called when the search is executed in InDesign plugin.
     *
     * Method can be overwritten in concrete custom field if a custom routing should be used.
     * By default, the search request is delegated to the executeSearch() method.
     *
     * @return string
     * @throws \Exception
     */
    protected function getSearchUrl(): string
    {
        $params = [
            'identifier'  => $this->project->getIdent(),
            'customField' => $this->getParam(),
        ];

        return $this->getUrlGenerator()
                    ->generate('mds_pimprint_custom_search', $params);
    }
}
