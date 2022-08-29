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

namespace Mds\PimPrint\CoreBundle\InDesign\CustomField\Traits;

/**
 * Trait SelectOptionsTrait
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\CustomField\Traits
 */
trait SelectOptionsTrait
{
    /**
     * Select field is multiple or not
     *
     * @var bool
     */
    private $multiple = false;

    /**
     * If values count greater than collapseAmount render as select field
     *
     * @var int
     */
    private $collapseAmount = 5;

    /**
     * Sets $multiple selection mode
     *
     * @param bool $multiple
     *
     * @return SelectOptionsTrait
     */
    public function setMultiple(bool $multiple = true)
    {
        $this->multiple = $multiple;

        return $this;
    }

    /**
     * Returns multiple selection mode
     *
     * @return bool
     */
    public function isMultiple(): bool
    {
        return $this->multiple;
    }

    /**
     * Sets $collapseAmount
     *
     * @param int $collapseAmount
     *
     * @return SelectOptionsTrait
     */
    public function setCollapseAmount(int $collapseAmount)
    {
        $this->collapseAmount = $collapseAmount;

        return $this;
    }

    /**
     * Convenience method to set select field to not collapse.
     *
     * @return SelectOptionsTrait
     */
    public function setNoCollapse()
    {
        $this->setCollapseAmount(0);

        return $this;
    }
}
