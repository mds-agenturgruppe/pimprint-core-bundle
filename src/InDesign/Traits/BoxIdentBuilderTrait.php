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
    protected static $identIndexes = [];

    /**
     * Array with all generated boxes to ensure unique box names.
     *
     * @var array
     */
    private static $generatedBoxes = [];

    /**
     * Postfix for generic ident generation.
     *
     * @var string
     */
    protected $genericPostfix = '';

    /**
     * Creates a unique box ident for $command.
     *
     * @param AbstractCommand $command
     *
     * @throws \Exception
     */
    protected function ensureBoxIdent(AbstractCommand $command)
    {
        if (false === $command instanceof AbstractBox) {
            return;
        }
        $boxIdent = $command->getBoxIdent();
        if (false === empty($boxIdent)) {
            $command->setBoxIdent($this->appendLocaleToBoxIdent($command, $boxIdent));
            $this->ensureUniqueBoxIdent($command);

            return;
        }

        $command->setBoxIdent(
            $this->appendLocaleToBoxIdent(
                $command,
                $this->buildGenericBoxIdent($command)
            )
        );
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
    protected function buildGenericBoxIdent(AbstractBox $command)
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
    protected function buildIdentIndex(string $commandName)
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
     */
    protected function setGenericPostfix(string $postfix)
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
    private function getPageNumber()
    {
        try {
            return $this->getCommandQueue()
                        ->getPageNumber();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Adds locale to $ident of $abstractBox is localized
     *
     * @param AbstractBox $abstractBox
     * @param string      $ident
     *
     * @return string
     * @throws \Exception
     */
    private function appendLocaleToBoxIdent(AbstractBox $abstractBox, string $ident): string
    {
        if (!$abstractBox->getLocalized()) {
            return $ident;
        }
        $language = $this->getProject()
                         ->getLanguage();

        if (empty($abstractBox->getLocale())) {
            $abstractBox->setLocale($language);
        }

        return $ident . '#' . $language;
    }
}
