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
    protected $blockCommands;

    /**
     * Block stack.
     *
     * @var array
     */
    protected $blockStack = [];

    /**
     * Renders all publication pages for $element.
     *
     * @param AbstractElement $element
     */
    protected function renderPages(AbstractElement $element)
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
    protected function hasBlockCommands()
    {
        return count($this->blockCommands) ? true : false;
    }

    /**
     * Stores current block commands into stack with $name.
     *
     * @param string $name
     * @param bool   $reset
     */
    protected function saveBlockCommands(string $name, bool $reset = true)
    {
        $this->blockStack[$name] = $this->blockCommands;
        if ($reset) {
            $this->resetBlockCommands();
        }
    }

    /**
     * Clears current blockCommands.
     */
    protected function resetBlockCommands()
    {
        $this->blockCommands = [];
    }

    /**
     * Adds $commands to current blockCommands.
     *
     * @param array $commands
     *
     * @return array|AbstractCommand[]
     */
    protected function addBlockCommands(array $commands)
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
     */
    protected function addToBlock(AbstractCommand $command)
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
    protected function restoreBlockCommands(string $name)
    {
        $commands = $this->getBlockCommands();
        $this->blockCommands = isset($this->blockStack[$name]) ? $this->blockStack[$name] : [];

        return $commands;
    }

    /**
     * Returns commands in current block.
     *
     * @return AbstractCommand[]
     */
    protected function getBlockCommands()
    {
        return $this->blockCommands;
    }
}
