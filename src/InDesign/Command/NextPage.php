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

use Mds\PimPrint\CoreBundle\Project\Traits\ProjectAwareTrait;

/**
 * Jumps to the next page in the InDesign document.
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command
 */
class NextPage extends AbstractCommand
{
    use ProjectAwareTrait;

    /**
     * Command name.
     *
     * @var string
     */
    const CMD = 'nextpage';

    /**
     * Allowed command.
     *
     * @var array
     */
    protected $availableParams = [
        'useautocmds'   => 0,
        'usepageoffset' => 0,
        'usedoublepage' => 0,
    ];

    /**
     * NextPage constructor.
     *
     * @param bool $useTemplate
     * @param bool $useDoublePage
     * @param bool $usePageOffset
     *
     * @throws \Exception
     */
    public function __construct(bool $useTemplate = true, bool $useDoublePage = false, bool $usePageOffset = false)
    {
        $this->setUseTemplate($useTemplate);
        $this->setUsePageOffset($usePageOffset);
        $this->setDoublePage($useDoublePage);
    }

    /**
     * Controls if last sent template should be used.
     *
     * @param bool $useTemplate
     *
     * @return NextPage
     * @throws \Exception
     */
    public function setUseTemplate(bool $useTemplate)
    {
        $this->setParam('useautocmds', $useTemplate ? 1 : 0);

        return $this;
    }

    /**
     * Use automatic x offset
     *
     * @param bool $usePageOffset
     *
     * @return NextPage
     * @throws \Exception
     */
    public function setUsePageOffset(bool $usePageOffset)
    {
        $this->setParam('usepageoffset', $usePageOffset ? 1 : 0);

        return $this;
    }

    /**
     * Set Double Pages
     *
     * @param bool $useDoublePage
     *
     * @return NextPage
     * @throws \Exception
     */
    public function setDoublePage(bool $useDoublePage)
    {
        $this->setParam('usedoublepage', $useDoublePage ? 1 : 0);

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
    public function buildCommand(bool $addCmd = true)
    {
        $return = parent::buildCommand($addCmd);
        $this->getCommandQueue()
             ->incrementPageNumber();

        return $return;
    }
}
