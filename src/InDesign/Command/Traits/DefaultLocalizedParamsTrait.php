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

namespace Mds\PimPrint\CoreBundle\InDesign\Command\Traits;

use Mds\PimPrint\CoreBundle\InDesign\Command\AbstractBox;

/**
 * Trait DefaultLocalizedParamsTrait
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command\Traits
 */
trait DefaultLocalizedParamsTrait
{
    /**
     * Default localized flag for AbstractBox type
     *
     * @var bool
     */
    private static bool $defaultLocalized = false;

    /**
     * Default mode for useMasterLocaleDimension for AbstractBox type
     *
     * @var string
     */
    private static string $defaultUseMasterLocaleMode = AbstractBox::USE_MASTER_LOCALE_ALL;

    /**
     * Sets $localized as default localized setting for AbstractBox type
     *
     * @param bool $localized
     *
     * @return void
     */
    public static function setDefaultLocalized(bool $localized = true): void
    {
        self::$defaultLocalized = $localized;
    }

    /**
     * Sets $mode as default useMasterLocaleDimension for AbstractBox type
     *
     * @param string $mode
     *
     * @return void
     * @throws \Exception
     */
    public static function setDefaultUseMasterLocaleMode(string $mode): void
    {
        self::validateUseMasterLocaleDimension($mode);
        self::$defaultUseMasterLocaleMode = $mode;
    }

    /**
     * Initializes localized params from AbstractBox with default values
     *
     * @return void
     * @throws \Exception
     */
    protected function initLocalizedParams(): void
    {
        $this->setLocalized(self::$defaultLocalized);
        $this->setUseMasterLocaleDimension(self::$defaultUseMasterLocaleMode);
    }
}
