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

namespace Mds\PimPrint\CoreBundle\InDesign\Command;

/**
 * Class CheckNewColumn
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command
 */
class CheckNewColumn extends AbstractCommand implements ComponentInterface, DynamicLayoutBreakInterface
{
    /**
     * Command name.
     *
     * @var string
     */
    const CMD = 'checkNewColumn';

    /**
     * Available command params with default values.
     *
     * @var array
     */
    protected array $availableParams = [
        'columnWidth'  => null,
        'columnMargin' => 0,
        'maxXPos'      => null,
    ];

    /**
     * CheckNewPage command for page end detection and pagination instruction
     *
     * @var CheckNewPage
     */
    private CheckNewPage $checkNewPage;

    /**
     * CheckNewColumn constructor.
     *
     * @param CheckNewPage          $checkNewPage
     * @param float|int|string|null $columnWidth  Width of each column
     * @param int|float|string|null $columnMargin Margin to be used for detecting pace left on page
     * @param int|float|string|null $maxXPos      Optional x Position on page used for white space on the right side
     *
     * @throws \Exception
     */
    public function __construct(
        CheckNewPage $checkNewPage,
        int|float|string $columnWidth = null,
        int|float|string $columnMargin = null,
        int|float|string $maxXPos = null,
    ) {
        $this->initParams($this->availableParams);
        $this->checkNewPage = $checkNewPage;

        if (null !== $columnWidth) {
            $this->setColumnWidth($columnWidth);
        }

        if (null !== $columnMargin) {
            $this->setColumnMargin($columnMargin);
        }

        if (null !== $maxXPos) {
            $this->setMaxXPos($maxXPos);
        }
    }

    /**
     * Sets $columnWidth
     *
     * @param float|int|string $columnWidth
     *
     * @return CheckNewColumn
     * @throws \Exception
     */
    public function setColumnWidth(int|float|string $columnWidth): CheckNewColumn
    {
        $this->setParam('columnWidth', $columnWidth);

        return $this;
    }

    /**
     * Sets $columnMargin
     *
     * @param float|int|string $columnMargin
     *
     * @return CheckNewColumn
     * @throws \Exception
     */
    public function setColumnMargin(int|float|string $columnMargin): CheckNewColumn
    {
        $this->setParam('columnMargin', $columnMargin);

        return $this;
    }

    /**
     * Sets $maxXPos.
     * Defines the optional white space on the right side of the page left out for column breaks.
     *
     * @param int|float|string $maxXPos
     *
     * @return CheckNewColumn
     * @throws \Exception
     */
    public function setMaxXPos(int|float|string $maxXPos): CheckNewColumn
    {
        $this->setParam('maxXPos', $maxXPos);

        return $this;
    }

    /**
     * Sets $checkNewPage
     *
     * @param CheckNewPage $checkNewPage
     *
     * @return CheckNewColumn
     */
    public function setCheckNewPage(CheckNewPage $checkNewPage): CheckNewColumn
    {
        $this->checkNewPage = $checkNewPage;

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function getComponentIdent(): string
    {
        return self::CMD;
    }

    /**
     * {@inheritDoc}
     *
     * @return bool
     */
    public function isMultipleComponent(): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     *
     * @param bool $addCmd
     *
     * @return array
     * @throws \Exception
     */
    public function buildCommand(bool $addCmd = true): array
    {
        $this->ensureParameters();
        $this->buildCheckNewPage();

        return parent::buildCommand($addCmd);
    }

    /**
     * Builds checkNewPage component
     *
     * @return void
     * @throws \Exception
     */
    private function buildCheckNewPage(): void
    {
        if (isset($this->checkNewPage)) {
            $this->addComponent($this->checkNewPage);

            return;
        }

        throw new \Exception('No CheckNewPage Command Page pagination defined in ' . static::CMD);
    }

    /**
     * Ensures all parameters are defined
     *
     * @return void
     * @throws \Exception
     */
    private function ensureParameters(): void
    {
        if (empty($this->getParam('columnWidth'))) {
            throw new \Exception('Parameter columnWidth not defined in ' . static::CMD);
        }
    }
}
