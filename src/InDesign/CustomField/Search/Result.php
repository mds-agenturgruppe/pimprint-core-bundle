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

namespace Mds\PimPrint\CoreBundle\InDesign\CustomField\Search;

/**
 * Class Result
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\CustomField\Search
 */
class Result
{
    /**
     * Search success indicator
     *
     * @var bool
     */
    private $success = true;

    /**
     * Search results array
     *
     * @var array
     */
    private $results = [];

    /**
     * Optional additional serach result data
     * @var mixed
     */
    private $additionalData = null;

    /**
     * Messages to show in InDesign Plugin if success is false
     *
     * @var array
     */
    private $messages = [];

    /**
     * Sets search result $success
     *
     * @param bool $success
     *
     * @return Result
     */
    public function setSuccess(bool $success): Result
    {
        $this->success = $success;

        return $this;
    }

    /**
     * Clears result array
     *
     * @return Result
     */
    public function clearResults(): Result
    {
        $this->results = [];

        return $this;
    }

    /**
     * Sets search $results
     *
     * @param array $results
     *
     * @return Result
     * @throws \Exception
     */
    public function setResults(array $results): Result
    {
        $this->results = [];

        foreach ($results as $result) {
            $this->addResult($result);
        }

        return $this;
    }

    /**
     * Sets search $results without validating format for value, label keys.
     * Use only when you really know what you do!
     *
     * @param array $results
     *
     * @return Result
     * @throws \Exception
     */
    public function setResultsRaw(array $results): Result
    {
        $this->results = $results;

        return $this;
    }

    /**
     * Adds search $result
     *
     * @param array $result
     *
     * @return Result
     * @throws \Exception
     */
    public function addResult(array $result): Result
    {
        if (!isset($result['label'])) {
            throw new \Exception("Search result must have a 'label' key");
        }
        if (!isset($result['value'])) {
            throw new \Exception("Search result must have a 'value' key");
        }
        $this->results[] = $result;

        return $this;
    }

    /**
     * Adds $value / $label result to results array
     *
     * @param string $value
     * @param string $label
     *
     * @return Result
     * @throws \Exception
     */
    final public function addResultRaw(string $value, string $label): Result
    {
        $this->addResult(
            [
                'value' => $value,
                'label' => $label
            ]
        );

        return $this;
    }

    /**
     * Returns true is results are in results array
     *
     * @return bool
     */
    public function hasResults(): bool
    {
        return !empty($this->results);
    }

    /**
     * Sets search result optional $additionalData
     *
     * @param mixed $additionalData
     *
     * @return Result
     */
    public function setAdditionalData($additionalData): Result
    {
        $this->additionalData = $additionalData;

        return $this;
    }

    /**
     * Adds $message to be displayed in InDesign Plugin when success is false
     *
     * @param string $message
     *
     * @return Result
     */
    public function addMessage(string $message): Result
    {
        $this->messages[] = $message;

        return $this;
    }

    /**
     * Builds response data
     *
     * @return array
     */
    public function buildResponseData(): array
    {
        return [
            'success'        => $this->success,
            'results'        => $this->results,
            'additionalData' => $this->additionalData,
            'messages'       => $this->messages
        ];
    }
}
