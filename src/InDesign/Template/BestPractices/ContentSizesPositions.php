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

namespace Mds\PimPrint\CoreBundle\InDesign\Template\BestPractices;

use Mds\PimPrint\CoreBundle\InDesign\Template\Concrete\A4PortraitTemplate;

/**
 * Example constants for content dimensions and positions.
 * Place these constants into your concrete implemented Template class.
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Template\BestPractices
 */
class ContentSizesPositions extends A4PortraitTemplate
{
    /**
     * Width of content area
     *
     * @var float
     */
    const CONTENT_WIDTH = self::PAGE_WIDTH - self::PAGE_MARGIN_LEFT - self::PAGE_MARGIN_RIGHT;

    /**
     * Height of content area
     *
     * @var float
     */
    const CONTENT_HEIGHT = self::PAGE_HEIGHT - self::PAGE_MARGIN_TOP - self::PAGE_MARGIN_BOTTOM;

    /**
     * Top yPos where content starts on a page
     *
     * @var float
     */
    const CONTENT_ORIGIN_TOP = self::PAGE_MARGIN_TOP;

    /**
     * Top xPos where content starts on a page
     *
     * @var float
     */
    const CONTENT_ORIGIN_LEFT = self::PAGE_MARGIN_LEFT;

    /**
     * Max yPos where is content placed in a page
     *
     * @var float
     */
    const CONTENT_BOTTOM = self::PAGE_HEIGHT - self::PAGE_MARGIN_BOTTOM;

    /**
     * Max xPos where is content placed in a page
     *
     * @var float
     */
    const CONTENT_RIGHT = self::PAGE_WIDTH - self::PAGE_MARGIN_RIGHT;
}
