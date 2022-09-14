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

namespace Mds\PimPrint\CoreBundle\Project\Traits;

use Mds\PimPrint\CoreBundle\InDesign\Command\AbstractCommand;
use Pimcore\Model\Element\AbstractElement;

/**
 * Trait StackedElementsRendering
 *
 * @package Mds\PimPrint\DemoBundle\Project\Traits
 */
trait BlockStackedQueueRenderingTrait
{
    use ElementCollectionRenderingTrait;

    /**
     * Command stack for current block.
     *
     * @var AbstractCommand[]
     */
    protected array $blockCommands = [];

    /**
     * Block stack.
     *
     * @var array
     */
    protected array $blockStack = [];

    /**
     * Renders all publication pages for $element.
     *
     * @param AbstractElement $element
     *
     * @return void
     */
    protected function renderPages(AbstractElement $element): void
    {
        $this->collectElements($element);
        while (true) {
            $this->resetBlockCommands();
            $element = $this->getNextElement();
            if (empty($element)) {
                break;
            }
            $this->renderElement($element);
            $this->addCommands($this->getBlockCommands());
        }
    }

    /**
     * Returns true if project has current block commands.
     *
     * @return bool
     */
    protected function hasBlockCommands(): bool
    {
        return (bool)count($this->blockCommands);
    }

    /**
     * Stores current block commands into stack with $name.
     *
     * @param string $name
     * @param bool   $reset
     *
     * @return void
     */
    protected function saveBlockCommands(string $name, bool $reset = true): void
    {
        $this->blockStack[$name] = $this->blockCommands;
        if ($reset) {
            $this->resetBlockCommands();
        }
    }

    /**
     * Clears current blockCommands.
     *
     * @return void
     */
    protected function resetBlockCommands(): void
    {
        $this->blockCommands = [];
    }

    /**
     * Adds $commands to current blockCommands.
     *
     * @param array $commands
     *
     * @return AbstractCommand[]
     */
    protected function addBlockCommands(array $commands): array
    {
        foreach ($commands as $command) {
            $this->blockCommands[] = $command;
        }

        return $this->blockCommands;
    }

    /**
     * Adds $command to current blockCommands.
     *
     * @param AbstractCommand $command
     *
     * @return void
     */
    protected function addToBlock(AbstractCommand $command): void
    {
        $this->blockCommands[] = $command;
    }

    /**
     * Restores stack commands $name to current blockCommands and returns the original current blockCommands.
     *
     * @param string $name
     *
     * @return AbstractCommand[]
     */
    protected function restoreBlockCommands(string $name): array
    {
        $commands = $this->getBlockCommands();
        $this->blockCommands = $this->blockStack[$name] ?? [];

        return $commands;
    }

    /**
     * Returns commands in current block.
     *
     * @return AbstractCommand[]
     */
    protected function getBlockCommands(): array
    {
        return $this->blockCommands;
    }
}
