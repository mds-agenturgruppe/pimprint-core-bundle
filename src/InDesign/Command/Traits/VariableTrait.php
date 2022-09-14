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

namespace Mds\PimPrint\CoreBundle\InDesign\Command\Traits;

use Mds\PimPrint\CoreBundle\InDesign\Command\AbstractBox;
use Mds\PimPrint\CoreBundle\InDesign\Command\Variable;

/**
 * Trait for setting variables when placing elements for relative positioning.
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command\Traits
 */
trait VariableTrait
{
    /**
     * Sets $position of placed element as variable $name in InDesign.
     *
     * @param string $name     Name of variable to set in InDesign.
     * @param string $position Relative box positions. Use Variable command POSITION constants.
     *
     * @return VariableTrait|AbstractBox
     * @throws \Exception
     */
    public function setVariable(string $name, string $position): AbstractBox|static
    {
        $this->validateVariablePosition($position);
        $this->addComponent(new Variable($name, $position));

        return $this;
    }

    /**
     * Validates $position value.
     *
     * @param string $position
     *
     * @return void
     * @throws \Exception
     */
    private function validateVariablePosition(string $position): void
    {
        if (false === in_array($position, Variable::$allowedPositions)) {
            throw new \Exception(sprintf("Invalid position '%s' in '%s'.", $position, static::class));
        }
    }
}
