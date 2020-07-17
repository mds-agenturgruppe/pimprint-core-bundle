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

/**
 * Interface ComponentInterface
 *
 * Interface for commands that can be components of commands.
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command
 */
interface ComponentInterface
{
    /**
     * Returns ident of command when used as compound.
     *
     * @return string
     */
    public function getComponentIdent(): string;

    /**
     * Returns true if component can be used multiple times in the same command.
     *
     * @return bool
     */
    public function isMultipleComponent(): bool;
}
