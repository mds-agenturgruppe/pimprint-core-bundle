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

namespace Mds\PimPrint\CoreBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;

/**
 * Class MdsPimPrintCoreBundle
 *
 * @package Mds\PimPrint\CoreBundle
 */
class MdsPimPrintCoreBundle extends AbstractPimcoreBundle
{
    use PackageVersionTrait;

    /**
     * Returns the composer package name used to resolve the version
     *
     * @return string
     */
    protected function getComposerPackageName(): string
    {
        return 'mds-agenturgruppe/pimprint-core-bundle';
    }

    /**
     * Bundle description as shown in extension manager
     *
     * @return string
     */
    public function getDescription()
    {
        return 'mds PimPrint CoreBundle - The InDesign Printing Solution for Pimcore.';
    }
}
