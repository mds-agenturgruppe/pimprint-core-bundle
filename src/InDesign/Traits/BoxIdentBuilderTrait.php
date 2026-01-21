<?php
/**
 * mds. Agenturgruppe GmbH
 *
 * This source file is available under the terms of the
 * mds. Commercial License (MCL)
 *
 * Full copyright and license information is available in
 * LICENSE.md, which is distributed with this source code.
 *
 * @copyright Copyright (c) mds. Agenturgruppe GmbH (https://www.mds.eu)
 * @license   mds. Commercial License (MCL)
 */

namespace Mds\PimPrint\CoreBundle\InDesign\Traits;

use Mds\PimPrint\CoreBundle\InDesign\Command\AbstractBox;
use Mds\PimPrint\CoreBundle\InDesign\Command\AbstractCommand;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

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
        if (!$command instanceof AbstractBox) {
            return;
        }
        $boxIdent = $command->getBoxIdent();
        if (!empty($boxIdent)) {
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
     * Builds a unique index for $command on the current page.
     *
     * @param string $commandName
     *
     * @return int
     */
    protected function buildIdentIndex(string $commandName): int
    {
        $page = $this->getPageNumber();
        if (!isset(self::$identIndexes[$page])) {
            self::$identIndexes[$page] = [];
        }
        if (!isset(self::$identIndexes[$page][$commandName])) {
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
     * If an element ident is used multiple times, an error PageMessage is generated.
     *
     * @param AbstractBox $command
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Exception
     */
    private function ensureUniqueBoxIdent(AbstractBox $command): void
    {
        $boxIdent = $command->getBoxIdent();
        try {
            if ($command->getLocalized()) {
                if (mb_substr_count($boxIdent, '#') > 1) {
                    throw new \Exception();
                }
            } else {
                if (str_contains($boxIdent, '#')) {
                    throw new \Exception();
                }
            }
        } catch (\Exception $exception) {
            throw new \Exception('Error: BoxIdent can not contain # char: ' . $boxIdent);
        }

        $ident = $command->getElementName() . '#' . $boxIdent;
        if (isset(self::$generatedBoxes[$ident])) {
            $this->commandQueue()
                 ->addPageMessage('Error: Duplicate BoxIdent found:' . $ident, true);
        }
        self::$generatedBoxes[$ident] = true;
    }

    /**
     * Returns the current page number from CommandQueue.
     * Only correct if no automatic pagination is used.
     *
     * @return int
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function getPageNumber(): int
    {
        try {
            return $this->commandQueue()
                        ->getPageNumber();
        } catch (\Exception) {
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
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
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
