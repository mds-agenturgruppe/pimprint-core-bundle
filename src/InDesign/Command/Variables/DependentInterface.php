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

namespace Mds\PimPrint\CoreBundle\InDesign\Command\Variables;

/**
 * Interface DependentInterface
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command\Variables
 */
interface DependentInterface
{
    /**
     * Returns an array with all variables command is dependent from.
     *
     * @return array
     */
    public function getDependentVariables(): array;
}
