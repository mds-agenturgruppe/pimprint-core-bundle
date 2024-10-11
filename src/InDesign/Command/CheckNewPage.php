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
 * Component for placement commands.
 *
 * If the placed element bottom is at a larger position than $maxXPos, the element is
 * automatically repositioned on the following page at $newYPos and $newXPos.
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command
 */
class CheckNewPage extends AbstractCommand implements ComponentInterface, DynamicLayoutBreakInterface
{
    /**
     * Command name.
     *
     * @var string
     */
    const CMD = 'checknewpage';

    /**
     * Available command params with default values.
     *
     * @var array
     */
    protected array $availableParams = [
        'pos'      => '',
        'newpos'   => '',
        'newpos_x' => null,
    ];

    /**
     * GoToPage constructor.
     *
     * @param string      $maxYPos Maximum allowed y position on page in mm.
     * @param string      $newYPos New y position in mm on next page.
     * @param string|null $newXPos Optional new x position in mm on next page.
     *
     * @throws \Exception
     */
    public function __construct(
        string $maxYPos = '',
        string $newYPos = '',
        string $newXPos = null,
        int|float|string $marginOffset = null,
    ) {
        $this->initParams($this->availableParams);

        $this->setMaxYPos($maxYPos);
        $this->setNewYPos($newYPos);
        $this->setNewXPos($newXPos);
        if ($marginOffset) {
            $this->setMarginOffset($marginOffset);
        }
    }

    /**
     * Sets maximum Y-Position where the placed box should end.
     *
     * @param float|int|string $maxYPos
     *
     * @return CheckNewPage
     * @throws \Exception
     */
    public function setMaxYPos(float|int|string $maxYPos): CheckNewPage
    {
        $this->setParam('pos', $maxYPos);

        return $this;
    }

    /**
     * Sets the new Y-Position on the following page where the box is replaced.
     *
     * @param float|int|string $newYPos
     *
     * @return CheckNewPage
     * @throws \Exception
     */
    public function setNewYPos(float|int|string $newYPos): CheckNewPage
    {
        $this->setParam('newpos', $newYPos);

        return $this;
    }

    /**
     * Sets the optional X-Position where the box is replaced.
     *
     * @param float|int|string|null $newXPos
     *
     * @return CheckNewPage
     * @throws \Exception
     */
    public function setNewXPos(float|int|string|null $newXPos): CheckNewPage
    {
        $this->setParam('newpos_x', $newXPos);

        return $this;
    }

    /**
     * Sets MarginOffset.
     *
     * Value is used in page end detection to ensure that after the end of the paced element minimum $marginOffset
     * sized vertical space is available on the page. Otherwise, the page break will be created, even if the last
     * placed element is still on the page.
     *
     * Alloys you to create "white space" at the end of a page and not to break things apart.
     *
     * @param float|int|string $marginOffset
     *
     * @return CheckNewPage
     * @throws \Exception
     */
    public function setMarginOffset(float|int|string $marginOffset): CheckNewPage
    {
        $this->setParam('marginOffset', $marginOffset);

        return $this;
    }

    /**
     * Returns ident of command when used as compound.
     *
     * @return string
     */
    public function getComponentIdent(): string
    {
        return static::CMD;
    }

    /**
     * Returns true if component can be used multiple times in the same command.
     *
     * @return bool
     */
    public function isMultipleComponent(): bool
    {
        return false;
    }
}
