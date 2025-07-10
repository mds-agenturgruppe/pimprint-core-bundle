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
