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
 * @todo    Add possible linking to Pimcore Object/Asset ids to enable updates between pages.
 */
trait BoxIdentBuilderTrait
{
    use ProjectAwareTrait;

    /**
     * Generated box idents.
     *
     * @var array
     */
    protected static $boxIdents = [];

    /**
     * Creates a unique box ident for $command.
     *
     * @param AbstractCommand $command
     * @param string          $page
     *
     * @throws \Exception
     */
    protected function createBoxIdent(AbstractCommand $command, $page = '')
    {
        if (false === $command instanceof AbstractBox) {
            return;
        }
        /* @var AbstractBox $command */
        try {
            $boxIdent = $command->getParam('tid');
            if (false === empty($boxIdent)) {
                return;
            }
        } catch (\Exception $e) {
            return;
        }
        $parts = [
            static::TID_PREFIX,
            $this->getPageNumber(),
            $command::CMD,
            $this->buildIdentIndex($command::CMD)
        ];
        $command->setBoxIdent(implode('-', $parts));
    }

    /**
     * Builds unique index for $command on current page.
     *
     * @param string $command
     *
     * @return int
     */
    protected function buildIdentIndex($command)
    {
        $page = $this->getPageNumber();
        if (false === isset(self::$boxIdents[$page])) {
            self::$boxIdents[$page] = [];
        }
        if (false === isset(self::$boxIdents[$page][$command])) {
            self::$boxIdents[$page][$command] = 0;
        }

        return ++self::$boxIdents[$this->getPageNumber()][$command];
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
}
