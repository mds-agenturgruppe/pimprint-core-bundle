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
     * Sets the InDesign fit mode of the box.
     *
     * @param string $fit Fit mode of image in image-box. Use FIT class constants.
     *
     * @return FitTrait|ImageBox|TextBox|Table
     * @throws \Exception
     */
    public function setFit(string $fit): ImageBox|Table|TextBox|static
    {
        $this->setParam('fit', $fit);

        return $this;
    }

    /**
     * Validates $fit value.
     *
     * @param string $fit
     *
     * @return void
     * @throws \Exception
     */
    protected function validateFit(string $fit): void
    {
        if (false === in_array($fit, $this->allowedFits)) {
            throw new \Exception(
                sprintf("Invalid fit '%s'. Use '%s' FIT_ constants.", $fit, static::class)
            );
        }
    }
}
