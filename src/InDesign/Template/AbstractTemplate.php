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

namespace Mds\PimPrint\CoreBundle\InDesign\Template;

/**
 * Class AbstractTemplate
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Template
 */
abstract class AbstractTemplate
{
    /**
     * Default InDesign page margin top.
     *
     * @var float
     */
    const PAGE_MARGIN_TOP = 12.7;

    /**
     * Default InDesign page margin bottom.
     *
     * @var float
     */
    const PAGE_MARGIN_BOTTOM = self::PAGE_MARGIN_TOP;

    /**
     * Default InDesign page margin top.
     *
     * @var float
     */
    const PAGE_MARGIN_LEFT = self::PAGE_MARGIN_TOP;

    /**
     * Default InDesign page margin top.
     *
     * @var float
     */
    const PAGE_MARGIN_RIGHT = self::PAGE_MARGIN_TOP;

    /**
     * Indicates that document used pacing pages.
     *
     * @var bool
     */
    const FACING_PAGES = false;

    /**
     * Facing page documents start with left or right page
     *
     * @var bool
     */
    const FACING_PAGE_START_ON_LEFT = true;

    /**
     * Default InDesign page bleed
     *
     * @var float
     */
    const PAGE_BLEED = 0.0;

    /**
     * Returns document page width
     *
     * @return float
     * @throws \Exception
     */
    public function getPageWidth(): float
    {
        if (empty(static::PAGE_WIDTH)) {
            throw new \Exception($this->getDimensionError('PAGE_WIDTH'));
        }

        return static::PAGE_WIDTH;
    }

    /**
     * Returns document page height
     *
     * @return float
     * @throws \Exception
     */
    public function getPageHeight(): float
    {
        if (empty(static::PAGE_HEIGHT)) {
            throw new \Exception($this->getDimensionError('PAGE_HEIGHT'));
        }

        return static::PAGE_HEIGHT;
    }

    /**
     * Returns missing dimension error
     *
     * @param string $string
     *
     * @return string
     */
    private function getDimensionError(string $string): string
    {
        $message = "Template has no defined '%s'.";
        $message .= 'Define it in your Template or use one from Mds\PimPrint\CoreBundle\InDesign\Template\Concrete';

        return sprintf($message, $string);
    }
}
