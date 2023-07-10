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
 * Class ImageBoxScaled
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command
 */
class ImageBoxScaled extends ImageBox
{
    /**
     * Sets xScale and yScale to $scale
     *
     * @param float $scale
     *
     * @return ImageBoxScaled
     * @throws \Exception
     */
    public function setScale(float $scale): ImageBoxScaled
    {
        $this->setXScale($scale);
        $this->setYScale($scale);

        return $this;
    }

    /**
     * Sets ImageBox xScale value to $scale.
     *
     * @param float $scale
     *
     * @return ImageBoxScaled
     * @throws \Exception
     */
    public function setXScale(float $scale): ImageBoxScaled
    {
        $this->setParam('xscale', $scale);

        return $this;
    }

    /**
     * Sets ImageBox yScale value to $scale.
     *
     * @param float $scale
     *
     * @return ImageBoxScaled
     * @throws \Exception
     */
    public function setYScale(float $scale): ImageBoxScaled
    {
        $this->setParam('yscale', $scale);

        return $this;
    }

    /**
     * Sets ImageBox xScroll to $offset.
     *
     * @param float $offset
     *
     * @return ImageBoxScaled
     * @throws \Exception
     */
    public function setXScroll(float $offset): ImageBoxScaled
    {
        $this->setParam('xscroll', $offset);

        return $this;
    }

    /**
     * Sets ImageBox yScroll to $offset.
     *
     * @param float $offset
     *
     * @return ImageBoxScaled
     * @throws \Exception
     */
    public function setYScroll(float $offset): ImageBoxScaled
    {
        $this->setParam('yscroll', $offset);

        return $this;
    }
}
