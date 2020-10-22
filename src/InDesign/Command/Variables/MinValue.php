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

namespace Mds\PimPrint\CoreBundle\InDesign\Command\Variables;

/**
 * Sets a variable to the math minimum value of other variables.
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command\Variables
 */
class MinValue extends AbstractMath
{
    /**
     * Used math operation.
     *
     * @var string
     */
    const MATH_OPERATION = 'min';

    /**
     * MinValue constructor.
     *
     * @param string $name Name of variable to set
     * @param array  $variables Variable names to calculate minimum value from.
     *
     * @throws \Exception
     */
    public function __construct($name, array $variables = [])
    {
        parent::__construct($name, $variables);
    }
}
