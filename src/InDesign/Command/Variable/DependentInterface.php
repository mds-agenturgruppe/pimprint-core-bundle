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

namespace Mds\PimPrint\CoreBundle\InDesign\Command\Variable;

/**
 * Interface DependentInterface
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command
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
