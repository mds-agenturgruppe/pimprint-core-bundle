<?php
/**
 * mds. Agenturgruppe GmbH
 *
 * This source file is available under the terms of the
 * mds. Commercial License (MCL)
 *
 * Full copyright and license information is available in
 * LICENSE.md, which is distributed with this source code.
 *
 * @copyright Copyright (c) mds. Agenturgruppe GmbH (https://www.mds.eu)
 * @license   mds. Commercial License (MCL)
 */

namespace Mds\PimPrint\CoreBundle\InDesign\Template\Concrete;

use Mds\PimPrint\CoreBundle\InDesign\Template\AbstractTemplate;

/**
 * Class LetterPortraitTemplate
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Template
 */
class LetterPortraitTemplate extends AbstractTemplate
{
    /**
     * Letter portrait page width in mm.
     *
     * @var float
     */
    const PAGE_WIDTH = 215.9;

    /**
     * Letter portrait page height in mm.
     *
     * @var int
     */
    const PAGE_HEIGHT = 279.4;
}
