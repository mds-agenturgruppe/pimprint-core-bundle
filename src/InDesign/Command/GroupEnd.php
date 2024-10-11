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

use Mds\PimPrint\CoreBundle\InDesign\Command\Traits\ElementNameTrait;
use Mds\PimPrint\CoreBundle\InDesign\Command\Traits\LayerTrait;
use Mds\PimPrint\CoreBundle\InDesign\Command\Traits\PositionTrait;
use Mds\PimPrint\CoreBundle\InDesign\Command\Traits\SizeTrait;
use Mds\PimPrint\CoreBundle\InDesign\Command\Traits\VariableTrait;
use Mds\PimPrint\CoreBundle\InDesign\Command\Variables\DependentInterface;

/**
 * Class GroupEnd
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command
 */
class GroupEnd extends AbstractCommand implements DependentInterface
{
    use ElementNameTrait, LayerTrait, PositionTrait, SizeTrait, VariableTrait;

    /**
     * Command name.
     *
     * @var string
     */
    const CMD = 'groupend';

    /**
     * Available command params with default values.
     *
     * @var array
     */
    protected array $availableParams = [
        'moveTo'         => null,
        'ungroupafter'   => 0,
        'checknewpage'   => null,
        'checkNewColumn' => null,
    ];

    /**
     * GroupEnd constructor.
     *
     * @param DynamicLayoutBreakInterface|null $layoutBreakCommand
     * @param bool                             $moveTo
     * @param bool                             $ungroupAfter
     *
     * @throws \Exception
     */
    public function __construct(
        DynamicLayoutBreakInterface $layoutBreakCommand = null,
        bool $moveTo = false,
        bool $ungroupAfter = false
    ) {
        $this->initParams($this->availableParams);

        if ($layoutBreakCommand) {
            $this->setLayoutBreak($layoutBreakCommand);
        }

        $this->setMoveTo($moveTo);
        $this->setUngroupAfter($ungroupAfter);
    }

    /**
     * Sets DynamicLayoutBreak $command
     *
     * @param DynamicLayoutBreakInterface $command
     *
     * @return GroupEnd
     * @throws \Exception
     */
    public function setLayoutBreak(DynamicLayoutBreakInterface $command): GroupEnd
    {
        switch (true) {
            case $command instanceof CheckNewPage:
                $this->setCheckNewPage($command);
                break;

            case $command instanceof CheckNewColumn:
                $this->setCheckNewColumn($command);
        }

        return $this;
    }

    /**
     * Alias for backwards compatibility
     *
     * @param CheckNewPage|null $checkNewPage
     *
     * @return GroupEnd
     * @throws \Exception
     * @deprecated
     */
    public function setNewPageCmd(CheckNewPage $checkNewPage = null): GroupEnd
    {
        return $this->setCheckNewPage($checkNewPage);
    }

    /**
     * Sets $checkNewPage directive.
     *
     * @param CheckNewPage $checkNewPage
     *
     * @return GroupEnd
     * @throws \Exception
     *
     */
    public function setCheckNewPage(CheckNewPage $checkNewPage): GroupEnd
    {
        $this->setParam('checknewpage', $checkNewPage);

        return $this;
    }

    /**
     * Sets $checkNewColumn directive.
     *
     * @param CheckNewColumn $checkNewColumn
     *
     * @return GroupEnd
     * @throws \Exception
     */
    public function setCheckNewColumn(CheckNewColumn $checkNewColumn): GroupEnd
    {
        $this->setParam('checkNewColumn', $checkNewColumn);

        return $this;
    }

    /**
     * Sets ungroup after postion calculate.
     *
     * @param bool $ungroupAfter
     *
     * @return GroupEnd
     * @throws \Exception
     */
    public function setUngroupAfter(bool $ungroupAfter): GroupEnd
    {
        $this->setParam('ungroupafter', $ungroupAfter ? 1 : 0);

        return $this;
    }

    /**
     * Sets $moveTo position of group
     *
     * @param bool $moveTo
     *
     * @return GroupEnd
     * @throws \Exception
     */
    public function setMoveTo(bool $moveTo): GroupEnd
    {
        $this->setParam('moveTo', $moveTo ? 1 : 0);

        return $this;
    }

    /**
     * Builds command array that is sent as JSON to InDesign.
     *
     * @param bool $addCmd
     *
     * @return array
     * @throws \Exception
     */
    public function buildCommand(bool $addCmd = true): array
    {
        $this->buildLayoutBreakParams();

        return parent::buildCommand($addCmd);
    }

    /**
     * Sets checkNewPage or checkNewColumn commands. One of them must be set
     *
     * @return void
     * @throws \Exception
     */
    private function buildLayoutBreakParams(): void
    {
        $hasCommand = false;

        $command = $this->getParam('checknewpage');
        if ($command instanceof CheckNewPage) {
            $this->addComponent($command);
            $hasCommand = true;
        }

        $command = $this->getParam('checkNewColumn');
        if ($command instanceof CheckNewColumn) {
            if ($hasCommand) {
                throw new \Exception(
                    'CheckNewPage already add to GroupEnd. Only Page or Column can be used'
                );
            }
            $this->addComponent($command);
        }
    }
}
