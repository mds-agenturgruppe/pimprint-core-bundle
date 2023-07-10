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

use Mds\PimPrint\CoreBundle\InDesign\Command\Traits\DefaultLocalizedParamsTrait;

/**
 * Places a copy of a box from template document.
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command
 */
class CopyBox extends AbstractBox
{
    use DefaultLocalizedParamsTrait;

    /**
     * Command name.
     *
     * @var string
     */
    const CMD = 'cbox';

    /**
     * CopyBox constructor.
     *
     * @param string     $elementName Name of template element.
     * @param float|null $left        Left position in mm.
     * @param float|null $top         Top position in mm.
     * @param float|null $width       Width of element in mm.
     * @param float|null $height      Height of element in mm.
     *
     * @throws \Exception
     */
    public function __construct(
        string $elementName = '',
        float $left = null,
        float $top = null,
        float $width = null,
        float $height = null
    ) {
        $this->initBoxParams();

        $this->initElementName();
        $this->initLayer();
        $this->initPosition();
        $this->initSize();

        $this->setElementName($elementName);
        $this->setLeft($left);
        $this->setTop($top);
        $this->setWidth($width);
        $this->setHeight($height);
        $this->initLocalizedParams();
    }
}
