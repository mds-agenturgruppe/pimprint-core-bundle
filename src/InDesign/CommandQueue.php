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

namespace Mds\PimPrint\CoreBundle\InDesign;

use Mds\PimPrint\CoreBundle\InDesign\Command\AbstractCommand;
use Mds\PimPrint\CoreBundle\InDesign\Command\ExecuteScript;
use Mds\PimPrint\CoreBundle\InDesign\Command\GoToPage;
use Mds\PimPrint\CoreBundle\InDesign\Command\ImageCollectorInterface;
use Mds\PimPrint\CoreBundle\InDesign\Command\OpenDocument;
use Mds\PimPrint\CoreBundle\InDesign\Command\PageMessage;
use Mds\PimPrint\CoreBundle\InDesign\Command\UpdateElements;
use Mds\PimPrint\CoreBundle\InDesign\Command\Variable;
use Mds\PimPrint\CoreBundle\InDesign\Command\Variables\AbstractMath;
use Mds\PimPrint\CoreBundle\InDesign\Command\Variables\DependentInterface as VariableDependentInterface;
use Mds\PimPrint\CoreBundle\InDesign\Traits\BoxIdentBuilderTrait;
use Mds\PimPrint\CoreBundle\Service\PluginParameters;
use Mds\PimPrint\CoreBundle\Service\ProjectsManager;

/**
 * Class CommandQueue
 *
 * @package Mds\PimPrint\CoreBundle\Indesign
 */
class CommandQueue
{
    use BoxIdentBuilderTrait;

    /**
     * Prefix for BoxIdent.
     *
     * @var string
     */
    const IDENT_PREFIX = 'Q';

    /**
     * Commands to send to InDesign.
     *
     * @var AbstractCommand[]
     */
    protected array $commands = [];

    /**
     * Last stored yPos.
     *
     * @var int|float
     */
    protected $yPos = 0;

    /**
     * Current page number. (Handle with care.)
     *
     * @var int
     */
    protected int $pageNumber = 0;

    /**
     * Array with registered variables via Variable or VariableTrait.
     * Used to verify existence of variables when a variable is used for relative positioning or calculation.
     *
     * @var array
     */
    protected array $registeredVariables = [];

    /**
     * Array with all assets used in generated publication.
     *
     * @var array
     */
    protected array $registeredAssets = [];

    /**
     * Array with missing assets used in generated publication.
     *
     * @var array
     */
    protected array $missingAssets = [
        'assetIds' => [],
        'elements' => 0,
    ];

    /**
     * Returns current pageNumber. (Handle with care.)
     *
     * @return int
     */
    public function getPageNumber(): int
    {
        return $this->pageNumber;
    }

    /**
     * Sets pageNumber.
     *
     * @param int $pageNumber
     *
     * @return CommandQueue
     */
    public function setPageNumber(int $pageNumber): CommandQueue
    {
        $this->pageNumber = $pageNumber;

        return $this;
    }

    /**
     * Increments pageNumber with $increment and returns the new pageNumber.
     *
     * @param int $increment
     *
     * @return int
     */
    public function incrementPageNumber(int $increment = 1): int
    {
        $this->pageNumber += $increment;

        return $this->pageNumber;
    }

    /**
     * Returns current yPosition.
     *
     * @return float|int
     */
    public function getYPos(): float|int
    {
        return $this->yPos;
    }

    /**
     * Sets $value as current yPosition.
     * If $sendCommand is true, value is set in InDesign via Variable command.
     *
     * @param float|int $value
     * @param bool      $sendCommand
     *
     * @return CommandQueue
     * @throws \Exception
     */
    public function setYPos(float|int $value, bool $sendCommand = false): CommandQueue
    {
        $this->yPos = $value;
        if ($sendCommand) {
            $this->addCommand(new Variable(Variable::VARIABLE_Y_POSITION, $this->yPos));
        }

        return $this;
    }

    /**
     * Increments current yPosition by $value and returns the new value.
     * If $sendCommand is true, value is set in InDesign via Variable command.
     *
     * @param float|int $value
     * @param bool      $sendCommand
     *
     * @return float
     * @throws \Exception
     */
    public function incrementYPos(float|int $value, bool $sendCommand = false): float
    {
        $this->setYPos($this->getYPos() + $value, $sendCommand);

        return $this->getYPos();
    }

    /**
     * Returns all generated commands.
     *
     * @return array
     */
    public function getCommandsRaw(): array
    {
        return $this->commands;
    }

    /**
     * Returns commands to send to InDesign Plugin filtered for selected elements.
     *
     * @return array
     * @throws \Exception
     */
    public function getCommands(): array
    {
        if (true === $this->getProject()
                          ->pluginParams()
                          ->isUpdateModeSelected()) {
            return $this->filterSelectedCommands($this->getCommandsRaw());
        }

        return $this->getCommandsRaw();
    }

    /**
     * Filters commands for selected elements in InDesign document.
     *
     * @param array $commands
     *
     * @return array
     * @throws \Exception
     */
    protected function filterSelectedCommands(array $commands): array
    {
        try {
            $selectedElements = $this->getProject()
                                     ->pluginParams()
                                     ->get(PluginParameters::PARAM_ELEMENT_LIST);
        } catch (\Exception) {
            return $commands;
        }
        $selectedElements = (array)$selectedElements;
        if (empty($selectedElements)) {
            return [];
        }
        if (ProjectsManager::isLocalizedProject()) {
            $this->removeLocaleFromSelectedElements($selectedElements);
        }

        $commandWhitelist = [
            OpenDocument::CMD,
            GoToPage::CMD,
            ExecuteScript::CMD,
            Variable::CMD,
            PageMessage::CMD
        ];

        $return = [];
        $updateElements = new UpdateElements($selectedElements);
        $return[] = $updateElements->buildCommand();
        foreach ($commands as $command) {
            if (isset($command['tid'])) {
                if (isset($command['localized']) && $command['localized']) {
                    $boxName = preg_replace(
                        '/#(\w{2,3}|\w{2,3}_\w{2,4}|\w{2,3}_\w{2,4}_\w{2,5})$/',
                        '#',
                        $command['tid']
                    );
                    $boxName = $command['name'] . '#' . $boxName;
                } else {
                    $boxName = $command['name'] . '#' . $command['tid'] . '#';
                }
                if (true === in_array($boxName, $selectedElements)) {
                    $return[] = $command;
                }
            } elseif (true === in_array($command['cmd'], $commandWhitelist)) {
                $return[] = $command;
            }
        }

        return $return;
    }

    /**
     * Adds $command to CommandQueue.
     *
     * @param AbstractCommand $command
     *
     * @return CommandQueue
     * @throws \Exception
     */
    public function addCommand(AbstractCommand $command): CommandQueue
    {
        $this->processVariables($command);
        $this->ensureBoxIdent($command);
        $array = $command->buildCommand();
        if (!empty($array)) {
            $this->commands[] = $array;
        }
        $this->registerAsset($command);

        return $this;
    }

    /**
     * Processes variables:
     * - Registers used variables
     * - Validates usage of variables
     *
     * @param AbstractCommand $command
     *
     * @return void
     * @throws \Exception
     */
    protected function processVariables(AbstractCommand $command): void
    {
        $this->registerVariables($command);
        $this->validateVariables($command);
    }

    /**
     * Checks if command sets a variable an registers it's name.
     *
     * @param AbstractCommand $command
     *
     * @return void
     * @throws \Exception
     */
    protected function registerVariables(AbstractCommand $command): void
    {
        if ($command instanceof Variable) {
            $this->registeredVariables[] = $command->getName();

            return;
        } elseif ($command instanceof AbstractMath) {
            $this->registeredVariables[] = $command->getName();

            return;
        }
        foreach ($command->getComponents() as $component) {
            $this->registerVariables($component);
        }
    }

    /**
     * Checks if command uses only existing variables.
     * If a variable doesn't exist an exception is thrown.
     *
     * @param AbstractCommand $command
     *
     * @return void
     * @throws \Exception
     */
    protected function validateVariables(AbstractCommand $command): void
    {
        if (false === $command instanceof VariableDependentInterface) {
            return;
        }
        $check = array_diff($command->getDependentVariables(), $this->registeredVariables);
        if (0 !== count($check)) {
            throw new \Exception(
                sprintf('Used relative position variables %s not defined.', implode(', ', $check))
            );
        }
    }

    /**
     * Convenience method to add a PageMessage command.
     *
     * @param string $message
     * @param bool   $onPage
     *
     * @return CommandQueue
     * @throws \Exception
     */
    public function addPageMessage(string $message, bool $onPage = false): CommandQueue
    {
        $this->addCommand(
            new PageMessage($message, $onPage)
        );

        return $this;
    }

    /**
     * Registers used asset.
     *
     * @param AbstractCommand $command
     *
     * @return void
     */
    private function registerAsset(AbstractCommand $command): void
    {
        if (false === $command instanceof ImageCollectorInterface) {
            return;
        }
        $this->registeredAssets += $command->getCollectedImages();
    }

    /**
     * Returns registered images.
     *
     * @return array
     */
    public function getRegisteredAssets(): array
    {
        return $this->registeredAssets;
    }

    /**
     * Increments missing asset counter for $assetId.
     *
     * @param int $assetId
     *
     * @return void
     */
    public function incrementMissingAssetCounter(int $assetId): void
    {
        if (false === isset($this->missingAssets['assetIds'][$assetId])) {
            $this->missingAssets['assetIds'][$assetId] = 0;
        }
        $this->missingAssets['assetIds'][$assetId]++;
        $this->missingAssets['elements']++;
    }

    /**
     * Returns missing assets.
     *
     * @return array
     */
    public function getMissingAssets(): array
    {
        return $this->missingAssets;
    }

    /**
     * Removes locale-idents from $selectedElements boxNames
     *
     * @param array $selectedElements
     *
     * @return void
     */
    private function removeLocaleFromSelectedElements(array &$selectedElements): void
    {
        foreach ($selectedElements as &$boxName) {
            $boxName = preg_replace('/#(\w{2,3}|\w{2,3}_\w{2,4}|\w{2,3}_\w{2,4}_\w{2,5})#$/', '#', $boxName);
        }
    }
}
