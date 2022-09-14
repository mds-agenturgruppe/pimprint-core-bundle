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
     *
     * @return void
     */
    protected function initSize(): void
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
     * @param float|int|null $width width in mm.
     *
     * @return SizeTrait|AbstractBox
     * @throws \Exception
     */
    public function setWidth(float|int|null $width): AbstractBox|static
    {
        $this->setParam('width', $width);

        return $this;
    }

    /**
     * Returns width parameter.
     *
     * @return int|float|null
     * @throws \Exception
     */
    public function getWidth(): float|int|null
    {
        return $this->getParam('width');
    }

    /**
     * Sets the height value in mm of the element.
     *
     * @param float|int|null $height height in mm.
     *
     * @return SizeTrait|AbstractBox
     * @throws \Exception
     */
    public function setHeight(float|int|null $height): AbstractBox|static
    {
        $this->setParam('height', $height);

        return $this;
    }

    /**
     * Returns height parameter.
     *
     * @return int|float|null
     * @throws \Exception
     */
    public function getHeight(): float|int|null
    {
        return $this->getParam('height');
    }

    /**
     * Validates width parameter.
     *
     * @param float|int|null $value
     *
     * @return void
     * @throws \Exception
     */
    protected function validateWidth(float|int|null $value): void
    {
        $this->validateSize($value, 'width');
    }

    /**
     * Validates height parameter.
     *
     * @param float|int|null $value
     *
     * @return void
     * @throws \Exception
     */
    protected function validateHeight(float|int|null $value): void
    {
        $this->validateSize($value, 'height');
    }
    /**
     * Converts $value to a valid value and checks of it isn't less than 0.
     *
     * @param mixed  $value
     * @param string $param
     *
     * @return void
     * @throws \Exception
     */
    protected function validateSize(mixed $value, string $param): void
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
    public function setResize(int $resize): AbstractBox|static
    {
        if (false === in_array($resize, $this->availableResizes)) {
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
     * @return void
     * @throws \Exception
     */
    protected function setAutoResize(): void
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
