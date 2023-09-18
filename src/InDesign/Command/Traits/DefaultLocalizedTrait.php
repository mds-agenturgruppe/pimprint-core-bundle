<?php
/**
 * mds Agenturgruppe GmbH
 *
 * This source file is licensed under GNU General Public License version 3 (GPLv3).
 *
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) mds. Agenturgruppe GmbH (https://www.mds.eu)
 */

namespace Mds\PimPrint\CoreBundle\InDesign\Command\Traits;

/**
 * Trait DefaultLocalizedTrait
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command\Traits
 */
trait DefaultLocalizedTrait
{
    /**
     * Default localized flag for AbstractBox type
     *
     * @var bool
     */
    private static $defaultLocalized = false;

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
     * Sets localized param value from AbstractBox type default flag
     *
     * @return void
     * @throws \Exception
     */
    protected function setDefaultLocalizedParam(): void
    {
        $this->setLocalized(self::$defaultLocalized);
    }
}
