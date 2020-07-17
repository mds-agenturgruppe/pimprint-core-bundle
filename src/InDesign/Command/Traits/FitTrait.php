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

use Mds\PimPrint\CoreBundle\InDesign\Command\ImageBox;
use Mds\PimPrint\CoreBundle\InDesign\Command\Table;
use Mds\PimPrint\CoreBundle\InDesign\Command\TextBox;

/**
 * Class FitTrait
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command\Traits
 */
trait FitTrait
{
    /**
     * Sets the InDesign fit mode of box.
     *
     * @param string $fit Fit mode of image in image-box. Use FIT class constants.
     *
     * @return FitTrait|ImageBox|TextBox|Table
     * @throws \Exception
     */
    public function setFit($fit)
    {
        $this->setParam('fit', $fit);

        return $this;
    }

    /**
     * Validates $fit value.
     *
     * @param string $fit
     *
     * @throws \Exception
     */
    protected function validateFit($fit)
    {
        if (false === in_array($fit, $this->allowedFits)) {
            throw new \Exception(
                sprintf("Invalid fit '%s'. Use '%s' FIT_ constants.", $fit, static::class)
            );
        }
    }
}
