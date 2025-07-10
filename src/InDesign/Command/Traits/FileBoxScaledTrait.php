<?php
/**
 * mds Agenturgruppe GmbH
 *
 * This source file is licensed under GNU General Public License version 3 (GPLv3).
 *
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) mds. Agenturgruppe GmbH (https://www.mds.eu)
 */

namespace Mds\PimPrint\CoreBundle\InDesign\Command\Traits;

use Mds\PimPrint\CoreBundle\InDesign\Command\FileBoxScaled;
use Mds\PimPrint\CoreBundle\InDesign\Command\ImageBox;
use Mds\PimPrint\CoreBundle\InDesign\Command\ImageBoxScaled;
use Mds\PimPrint\CoreBundle\InDesign\Command\Table;
use Mds\PimPrint\CoreBundle\InDesign\Command\TextBox;

/**
 * Trait FileBoxScaledTrait
 *
 * FileBox and ImageBox offers the offset placement (scrolling) of images placed in an image box.
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command\Traits
 */
trait FileBoxScaledTrait
{
    /**
     * ImageBoxScaled does not support fitting.
     *
     * Fitting would be executed after the scroll, resetting the scroll.
     * Assets are always placed in original size. Size of the asset must be set manually by scale percentage.
     *
     * @param string $fit
     *
     * @return ImageBox|Table|TextBox|FileBoxScaledTrait
     */
    public function setFit(string $fit): ImageBox|Table|TextBox|static
    {
        return $this;
    }

    /**
     * Moves box content by $offset mm horizontally.
     *
     * @param float $offset
     *
     * @return FileBoxScaled|ImageBoxScaled
     * @throws \Exception
     */
    public function setXScroll(float $offset): FileBoxScaled|ImageBoxScaled
    {
        $this->setParam('xscroll', $offset);

        return $this;
    }

    /**
     * Moves box content by $offset mm vertically.
     *
     * @param float $offset
     *
     * @return FileBoxScaled|ImageBoxScaled
     * @throws \Exception
     */
    public function setYScroll(float $offset): FileBoxScaled|ImageBoxScaled
    {
        $this->setParam('yscroll', $offset);

        return $this;
    }

    /**
     * Sets xScale and yScale to $scale percentage relative to the original asset size.
     * Default is 100.0.
     *
     * @param float $scale
     *
     * @return FileBoxScaled|ImageBoxScaled
     * @throws \Exception
     */
    public function setScale(float $scale = 100.0): FileBoxScaled|ImageBoxScaled
    {
        $this->setXScale($scale);
        $this->setYScale($scale);

        return $this;
    }

    /**
     * Sets $scale percentage for content width relative to the original asset size.
     * Default is 100.0.
     *
     * @param float $scale
     *
     * @return FileBoxScaled|ImageBoxScaled
     * @throws \Exception
     */
    public function setXScale(float $scale = 100.0): FileBoxScaled|ImageBoxScaled
    {
        $this->setParam('xscale', $scale);

        return $this;
    }

    /**
     * Sets $scale percentage for content height relative to the original asset size.
     * Default is 100.0.
     *
     * @param float $scale
     *
     * @return FileBoxScaled|ImageBoxScaled
     * @throws \Exception
     */
    public function setYScale(float $scale = 100.0): FileBoxScaled|ImageBoxScaled
    {
        $this->setParam('yscale', $scale);

        return $this;
    }
}
