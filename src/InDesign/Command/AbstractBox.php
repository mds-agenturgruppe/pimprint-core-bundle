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

use Mds\PimPrint\CoreBundle\InDesign\Command\Traits\LayerTrait;
use Mds\PimPrint\CoreBundle\InDesign\Command\Traits\ElementNameTrait;
use Mds\PimPrint\CoreBundle\InDesign\Command\Traits\PositionTrait;
use Mds\PimPrint\CoreBundle\InDesign\Command\Traits\SizeTrait;
use Mds\PimPrint\CoreBundle\InDesign\Command\Traits\VariableTrait;
use Mds\PimPrint\CoreBundle\InDesign\Command\Variables\DependentInterface;
use Mds\PimPrint\CoreBundle\Project\Traits\ProjectAwareTrait;

/**
 * Abstract command with generic functionality for box placement commands.
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command
 */
abstract class AbstractBox extends AbstractCommand implements DependentInterface
{
    use ElementNameTrait, LayerTrait, PositionTrait, SizeTrait, VariableTrait, ProjectAwareTrait;

    /**
     * No resize.
     *
     * @var int
     */
    const RESIZE_NO_RESIZE = 0;

    /**
     * Resize width and height.
     *
     * @var int
     */
    const RESIZE_WIDTH_HEIGHT = 1;

    /**
     * Resize width.
     *
     * @var int
     */
    const RESIZE_WIDTH = 2;

    /**
     * Resize height.
     *
     * @var int
     */
    const RESIZE_HEIGHT = 3;

    /**
     * Available command params with default values.
     *
     * @var array
     */
    private array $availableParams = [
        'tid'       => null,
        'cmdfilter' => null,
    ];

    /**
     * Available resize values.
     *
     * @var array
     */
    protected array $availableResizes = [
        self::RESIZE_NO_RESIZE,
        self::RESIZE_WIDTH_HEIGHT,
        self::RESIZE_WIDTH,
        self::RESIZE_HEIGHT,
    ];

    /**
     * Initializes abstract box.
     */
    protected function initBoxParams()
    {
        $this->initParams($this->availableParams);
    }

    /**
     * Sets box ident.
     *
     * @param string|null $ident
     *
     * @return AbstractBox
     * @throws \Exception
     */
    public function setBoxIdent(string $ident = null): AbstractBox
    {
        $this->setParam('tid', $ident);

        return $this;
    }

    /**
     * Returns box ident.
     *
     * @return string|null
     */
    public function getBoxIdent(): ?string
    {
        try {
            return $this->getParam('tid');
        } catch (\Exception) {
            return null;
        }
    }

    /**
     * Sets the ident of the box referenced to RenderingTrait::$boxIdentReference with $ident as postfix.
     * Used to create content referenced boxes in InDesign for content aware updates.
     *
     * @param string $ident
     *
     * @return AbstractBox
     * @throws \Exception
     * @see \Mds\PimPrint\CoreBundle\Project\Traits\RenderingTrait::$boxIdentReference
     */
    public function setBoxIdentReferenced(string $ident = ''): AbstractBox
    {
        try {
            $reference = $this->getProject()
                              ->getBoxIdentReference();
        } catch (\Exception) {
            $reference = '';
        }
        $this->setBoxIdent('ID-' . $reference . $ident);

        return $this;
    }

    /**
     * Sets cmdfilter for autocommands.
     *
     * @param string $filter
     *
     * @return AbstractBox
     * @throws \Exception
     */
    public function setCmdFilter(string $filter): AbstractBox
    {
        $this->setParam('cmdfilter', $filter);

        return $this;
    }

    /**
     * Validates command
     *
     * @return void
     * @throws \Exception
     */
    protected function validate(): void
    {
        $this->validateElementNameParam();
        $this->setAutoResize();
    }
}
