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
 * Class A1PortraitTemplate
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Template
 */
class A1PortraitTemplate extends AbstractTemplate
{
    /**
     * A1 portrait page width in mm.
     *
     * @var float
     */
    const PAGE_WIDTH = 594;

    /**
     * A1 portrait page height in mm.
     *
     * @var int
     */
    const PAGE_HEIGHT = 841;
}
