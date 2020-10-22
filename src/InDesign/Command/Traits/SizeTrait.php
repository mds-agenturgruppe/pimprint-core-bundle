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

/**
 * Trait to add params for resizing to a command.
 * The params width height are used to size a element in the document.
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command\Traits
 */
trait SizeTrait
{
    /**
     * Initializes trait
     */
    protected function initSize()
    {
        $this->initParams(
            [
                'width'  => null,
                'height' => null,
                'resize' => AbstractBox::RESIZE_NO_RESIZE,
            ]
        );
    }

    /**
     * Sets the width value in mm of the the element.
     *
     * @param int|float|null $width width in mm.
     *
     * @return SizeTrait|AbstractBox
     * @throws \Exception
     */
    public function setWidth($width)
    {
        $this->setParam('width', $width);

        return $this;
    }

    /**
     * Returns width parameter.
     *
     * @return int|float|null
     */
    public function getWidth()
    {
        return $this->getParam('width');
    }

    /**
     * Sets the height value in mm of the the element.
     *
     * @param int|float|null $height height in mm.
     *
     * @return SizeTrait|AbstractBox
     * @throws \Exception
     */
    public function setHeight($height)
    {
        $this->setParam('height', $height);

        return $this;
    }

    /**
     * Returns height parameter.
     *
     * @return int|float|null
     */
    public function getHeight()
    {
        return $this->getParam('height');
    }

    /**
     * Validates width parameter.
     *
     * @param int|float|null $value
     *
     * @throws \Exception
     */
    protected function validateWidth($value)
    {
        $this->validateSize($value, 'width');
    }

    /**
     * Validates height parameter.
     *
     * @param int|float|null $value
     *
     * @throws \Exception
     */
    protected function validateHeight($value)
    {
        $this->validateSize($value, 'height');
    }
    /**
     * Converts $value to a valid value and checks of it isn't less than 0.
     *
     * @param mixed  $value
     * @param string $param
     *
     * @throws \Exception
     */
    protected function validateSize($value, $param)
    {
        if (null === $value) {
            return;
        }
        if (is_string($value)) {
            $value = (float) $value;
        }
        if ($value < 0) {
            throw new \Exception(
                sprintf("Size parameter '%s' must be greater than 0 in '%s'.", $param, static::class)
            );
        }
    }

    /**
     * Sets the resize parameter for box.
     *
     * @param int $resize Use AbstractBox RESIZE constants.
     *
     * @return SizeTrait|AbstractBox
     * @throws \Exception
     */
    public function setResize(int $resize)
    {
        if (false === in_array($resize, $this->availibleResizes)) {
            throw new \Exception(
                sprintf("Invalid 'resize' parameter value '%s' in '%s'.", $resize, static::class)
            );
        }
        $this->setParam('resize', $resize);

        return $this;
    }

    /**
     * Sets automatic resize param if needed.
     *
     * @throws \Exception
     */
    protected function setAutoResize()
    {
        $resize = $this->getParam('resize');
        if ($resize !== AbstractBox::RESIZE_NO_RESIZE) {
            return;
        }
        $width = $this->getParam('width');
        $height = $this->getParam('height');

        if (false === empty($width) || false === empty($height)) {
            $this->setResize(AbstractBox::RESIZE_WIDTH_HEIGHT);
        }
    }
}
