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
 * Opens an InDesign document.
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command
 */
class OpenDocument extends AbstractCommand
{
    /**
     * Command name.
     *
     * @var string
     */
    const CMD = 'opendoc';

    /**
     * Defines to use current open document as document to generate to.
     *
     * @var string
     */
    const TYPE_USECURRENT = 'usecurrent';

    /**
     * Defined document to open as used template file.
     *
     * @var string
     */
    const TYPE_TEMPLATE = 'template';

    /**
     * Available command params with default values.
     *
     * @var array
     */
    protected array $availableParams = [
        'type'     => self::TYPE_USECURRENT,
        'language' => '',
        'name'     => null,
    ];

    /**
     * OpenDocument constructor.
     *
     * @param string $type
     * @param string $language
     * @param string $filename
     *
     * @throws \Exception
     */
    public function __construct(string $type = '', string $language = '', string $filename = '')
    {
        $this->initParams($this->availableParams);

        if (false === empty($type)) {
            $this->setType($type);
        }
        $this->setLanguage($language);
        $this->setFilename($filename);
    }

    /**
     * Sets type of document to open. Use TYPE constants for valid values.
     *
     * @param string $type
     *
     * @return OpenDocument
     * @throws \Exception
     */
    public function setType(string $type): OpenDocument
    {
        $this->setParam('type', $type);

        return $this;
    }

    /**
     * Language to set in the opened document.
     *
     * @param string $language
     *
     * @return OpenDocument
     * @throws \Exception
     */
    public function setLanguage(string $language): OpenDocument
    {
        $this->setParam('language', $language);

        return $this;
    }

    /**
     * Filename of InDesign file to open.
     *
     * @param string $filename
     *
     * @return OpenDocument
     * @throws \Exception
     */
    public function setFilename(string $filename): OpenDocument
    {
        if ('' == $filename) {
            $filename = null;
        }
        $this->setParam('name', $filename);

        return $this;
    }

    /**
     * Validates $value for param $type is allowed.
     *
     * @param string $value
     *
     * @throws \Exception
     */
    protected function validateType(string $value)
    {
        switch ($value) {
            case self::TYPE_USECURRENT:
            case self::TYPE_TEMPLATE:
                return;
        }
        throw new \Exception(sprintf("Invalid type '%s'. Use '%s' TYPE_ constants.", $value, self::class));
    }
}
