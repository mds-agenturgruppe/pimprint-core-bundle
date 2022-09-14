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

namespace Mds\PimPrint\CoreBundle\InDesign\Traits;

use Mds\PimPrint\CoreBundle\InDesign\Command\AbstractBox;
use Mds\PimPrint\CoreBundle\InDesign\Command\AbstractCommand;
use Mds\PimPrint\CoreBundle\Project\Traits\ProjectAwareTrait;

/**
 * Trait BoxIdentBuilderTrait
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Traits
 */
trait BoxIdentBuilderTrait
{
    use ProjectAwareTrait;

    /**
     * Array with ident indexes.
     *
     * @var array
     */
    protected static array $identIndexes = [];

    /**
     * Array with all generated boxes to ensure unique box names.
     *
     * @var array
     */
    private static array $generatedBoxes = [];

    /**
     * Postfix for generic ident generation.
     *
     * @var string
     */
    protected string $genericPostfix = '';

    /**
     * Creates a unique box ident for $command.
     *
     * @param AbstractCommand $command
     *
     * @return void
     * @throws \Exception
     */
    protected function ensureBoxIdent(AbstractCommand $command): void
    {
        if (false === $command instanceof AbstractBox) {
            return;
        }
        /* @var AbstractBox $command */
        $boxIdent = $command->getBoxIdent();
        if (false === empty($boxIdent)) {
            $this->ensureUniqueBoxIdent($command);

            return;
        }
        $command->setBoxIdent($this->buildGenericBoxIdent($command));
        $this->ensureUniqueBoxIdent($command);
    }

    /**
     * Builds generic boxIdent for $command.
     *
     * @param AbstractBox $command
     *
     * @return string
     * @throws \Exception
     */
    protected function buildGenericBoxIdent(AbstractBox $command): string
    {
        $parts = [
            $command::CMD,
            $this->genericPostfix,
            $this->getProject()
                 ->getBoxIdentGenericPostfix(),
        ];
        $ident = implode('', array_filter($parts));
        $parts = [
            static::IDENT_PREFIX,
            $this->getPageNumber(),
            $ident,
            $this->buildIdentIndex($ident)
        ];

        return implode('-', $parts);
    }

    /**
     * Builds unique index for $command on current page.
     *
     * @param string $commandName
     *
     * @return int
     */
    protected function buildIdentIndex(string $commandName): int
    {
        $page = $this->getPageNumber();
        if (false === isset(self::$identIndexes[$page])) {
            self::$identIndexes[$page] = [];
        }
        if (false === isset(self::$identIndexes[$page][$commandName])) {
            self::$identIndexes[$page][$commandName] = 0;
        }

        return ++self::$identIndexes[$this->getPageNumber()][$commandName];
    }

    /**
     * Sets genericPostfix.
     *
     * @param string $postfix
     *
     * @return void
     */
    protected function setGenericPostfix(string $postfix): void
    {
        $this->genericPostfix = $postfix;
    }

    /**
     * Ensured unique element names in InDesign.
     * If a element ident is used multiple times an error PageMessage is generated.
     *
     * @param AbstractBox $command
     *
     * @throws \Exception
     */
    private function ensureUniqueBoxIdent(AbstractBox $command)
    {
        $ident = $command->getElementName() . '#' . $command->getBoxIdent();
        if (isset(self::$generatedBoxes[$ident])) {
            $this->getCommandQueue()
                 ->addPageMessage('Error: Duplicate BoxIdent found:' . $ident, true);
        }
        self::$generatedBoxes[$ident] = true;
    }

    /**
     * Returns current page number from CommandQueue.
     *
     * @return int
     */
    private function getPageNumber(): int
    {
        try {
            return $this->getCommandQueue()
                        ->getPageNumber();
        } catch (\Exception) {
            return 0;
        }
    }
}
