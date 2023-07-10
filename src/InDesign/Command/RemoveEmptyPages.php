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
 * Class RemoveEmptyPages
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command
 */
class RemoveEmptyPages extends ExecuteScript
{
    /**
     * RemoveEmptyPages constructor
     *
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct('PimPrintHelper.Document.removeEmptyPages();');
    }
}
