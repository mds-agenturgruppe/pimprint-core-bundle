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

use Mds\PimPrint\CoreBundle\InDesign\Command\Traits\ImageCollectorTrait;
use Mds\PimPrint\CoreBundle\InDesign\Traits\BoxIdentBuilderTrait;

/**
 * Class SplitTable
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command
 */
class SplitTable extends AbstractCommand implements ImageCollectorInterface
{
    use BoxIdentBuilderTrait;
    use ImageCollectorTrait;

    /**
     * Command name.
     *
     * @var string
     */
    const CMD = 'splittable';

    /**
     * Builds command array that is sent as JSON to InDesign.
     *
     * @var
     */
    const IDENT_PREFIX = 'ST';

    /**
     * Available command params with default values.
     *
     * @var array
     */
    private $availableParams = [
        'table'          => null,
        'checknewpage'   => null,
        'precommands'    => [],
        'widowRowCount'  => 1,
        'orphanRowCount' => 1,
    ];

    /**
     * Array with commands to execute before split table is rendered on new page.
     *
     * @var AbstractCommand[]
     */
    protected $preCommands = [];

    /**
     * SplitTable constructor.
     *
     * @param Table|null        $table          Table command to split.
     * @param CheckNewPage|null $checkPage      CheckNewPage command for defined page break.
     * @param array             $preCommands    Optional preCommands to render before split table.
     * @param int               $widowRowCount  Number of widow rows.
     * @param int               $orphanRowCount Number of orphan rows.
     *
     * @throws \Exception
     */
    public function __construct(
        Table $table = null,
        CheckNewPage $checkPage = null,
        array $preCommands = [],
        int $widowRowCount = 1,
        int $orphanRowCount = 1
    ) {
        $this->initParams($this->availableParams);
        if ($table instanceof Table) {
            $this->setTable($table);
        }
        if ($checkPage instanceof CheckNewPage) {
            $this->setCheckNewPage($checkPage);
        }
        if (true === is_array($preCommands)) {
            $this->setPreCommands($preCommands);
        }
        $this->setWidowRowCount($widowRowCount)
             ->setOrphanRowCount($orphanRowCount);
    }

    /**
     * Sets $table to split.
     *
     * @param Table $table
     *
     * @return SplitTable
     * @throws \Exception
     */
    public function setTable(Table $table)
    {
        $this->setParam('table', $table);

        return $this;
    }

    /**
     * Sets $checkNewPage directive.
     *
     * @param CheckNewPage $checkNewPage
     *
     * @return $this
     * @throws \Exception
     */
    public function setCheckNewPage(CheckNewPage $checkNewPage)
    {
        $this->setParam('checknewpage', $checkNewPage);

        return $this;
    }

    /**
     * Returns registered pre commands.
     *
     * @return AbstractCommand[]
     */
    public function getPreCommands()
    {
        return $this->preCommands;
    }

    /**
     * Registers pre commands.
     *
     * @param AbstractCommand[] $preCommands
     *
     * @return SplitTable
     */
    public function setPreCommands(array $preCommands = [])
    {
        foreach ($preCommands as $preCommand) {
            if ($preCommand instanceof AbstractCommand) {
                $this->addPreCommand($preCommand);
            }
        }

        return $this;
    }

    /**
     * Adds $command to pre commands.
     *
     * @param AbstractCommand $commands
     *
     * @return SplitTable
     */
    public function addPreCommand(AbstractCommand $commands)
    {
        $this->preCommands[] = $commands;

        return $this;
    }

    /**
     * Sets minimum rows to show in split table.
     *
     * @param int $minRows
     *
     * @return SplitTable
     * @throws \Exception
     */
    public function setMinRows(int $minRows)
    {
        $this->setParam('minrows', $minRows);

        return $this;
    }

    /**
     * Validates if $minRows command is greater than 0.
     *
     * @param int $minRows
     *
     * @throws \Exception
     */
    protected function validateMinRows(int $minRows)
    {
        $this->validateGreaterZero($minRows, 'minRows');
    }

    /**
     * Sets widowRowCount.
     *
     * @param int $widowRowCount
     *
     * @return SplitTable
     * @throws \Exception
     */
    public function setWidowRowCount(int $widowRowCount)
    {
        $this->setParam('widowRowCount', $widowRowCount);

        return $this;
    }

    /**
     * Validates if $value for widowRowCount is greater than 0.
     *
     * @param int $value
     *
     * @throws \Exception
     */
    protected function validateWidowRowCount(int $value)
    {
        $this->validateGreaterZero($value, 'widowRowCount');
    }

    /**
     * Sets orphanRowCount.
     *
     * @param int $orphanRowCount
     *
     * @return SplitTable
     * @throws \Exception
     */
    public function setOrphanRowCount(int $orphanRowCount)
    {
        $this->setParam('orphanRowCount', $orphanRowCount);

        return $this;
    }

    /**
     * Validates if $value for orphanRowCount is greater than 0.
     *
     * @param int $value
     *
     * @throws \Exception
     */
    protected function validateOrphanRowCount(int $value)
    {
        $this->validateGreaterZero($value, 'orphanRowCount');
    }

    /**
     * Validates if $value command is greater than 0 for $param.
     *
     * @param int    $value
     * @param string $param
     *
     * @throws \Exception
     */
    private function validateGreaterZero(int $value, string $param)
    {
        if ($value < 1) {
            throw new \Exception(
                sprintf(
                    'Value for %s in %s must be at least 1. Tried to set %s.',
                    $param,
                    static::CMD,
                    $value
                )
            );
        }
    }

    /**
     * Builds command array that is sent as JSON to InDesign.
     *
     * @param bool $addCmd
     *
     * @return array
     * @throws \Exception
     */
    public function buildCommand(bool $addCmd = true)
    {
        try {
            $table = $this->getParam('table');
            if (false === $table instanceof Table) {
                throw new \Exception();
            }
        } catch (\Exception $e) {
            throw new \Exception('No table to split defined in ' . static::CMD);
        }
        $this->ensureBoxIdent($table);
        $this->addComponent($table);
        $this->addCollectedImages($table);

        try {
            $checkPage = $this->getParam('checknewpage');
            if (false === $checkPage instanceof CheckNewPage) {
                throw new \Exception();
            }
        } catch (\Exception $e) {
            throw new \Exception('No check new page directive for splitting defined in ' . static::CMD);
        }
        $this->addComponent($checkPage);

        $preCommands = [];
        foreach ($this->preCommands as $preCommand) {
            $preCommands[] = $preCommand->buildCommand();
            if ($preCommand instanceof ImageCollectorInterface) {
                $this->addCollectedImages($preCommand);
            }
        }
        $this->setParam('precommands', $preCommands);

        return parent::buildCommand($addCmd);
    }
}
