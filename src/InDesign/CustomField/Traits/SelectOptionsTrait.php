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
    private bool $multiple = false;

    /**
     * If values count greater than collapseAmount render as select field
     *
     * @var int
     */
    private int $collapseAmount = 5;

    /**
     * Sets $multiple selection mode
     *
     * @param bool $multiple
     *
     * @return SelectOptionsTrait
     */
    public function setMultiple(bool $multiple = true): static
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
    public function setCollapseAmount(int $collapseAmount): static
    {
        $this->collapseAmount = $collapseAmount;

        return $this;
    }

    /**
     * Convenience method to set a select field to not collapse.
     *
     * @return SelectOptionsTrait
     */
    public function setNoCollapse(): static
    {
        $this->setCollapseAmount(0);

        return $this;
    }
}
