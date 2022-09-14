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
 * Sets the target page in the InDesign document.
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command
 */
class GoToPage extends AbstractCommand
{
    use ProjectAwareTrait;

    /**
     * Command name.
     *
     * @var string
     */
    const CMD = 'gotopage';

    /**
     * Available command params with default values.
     *
     * @var array
     */
    protected array $availableParams = [
        'pagenumber'  => 1,
        'usepageoffset'  => 0,
        'useautocmds' => 0,
        'usedoublepage'  => 0,
    ];

    /**
     * GoToPage constructor.
     *
     * @param int  $page
     * @param bool $useTemplate
     * @param bool $useDoublePage
     * @param bool $usePageOffset
     *
     * @throws \Exception
     */
    public function __construct(
        int $page = 1,
        bool $useTemplate = true,
        bool $useDoublePage = false,
        bool $usePageOffset = false
    ) {
        $this->initParams($this->availableParams);

        $this->setPage($page);
        $this->setUseTemplate($useTemplate);
        $this->setUsePageOffset($usePageOffset);
        $this->setDoublePage($useDoublePage);
    }

    /**
     * Sets active page in InDesign document.
     *
     * @param int $page
     *
     * @return GoToPage
     * @throws \Exception
     */
    public function setPage(int $page): GoToPage
    {
        $this->setParam('pagenumber', $page);

        return $this;
    }

    /**
     * Validates page parameter.
     *
     * @param int $page
     *
     * @return void
     * @throws \Exception
     */
    protected function validatePage(int $page): void
    {
        if ($page < 1) {
            throw new \Exception("Page must be greater than 0.");
        }
    }

    /**
     * Controls if last sent template should be used.
     *
     * @param bool $useTemplate
     *
     * @return GoToPage
     * @throws \Exception
     */
    public function setUseTemplate(bool $useTemplate): GoToPage
    {
        $this->setParam('useautocmds', $useTemplate ? 1 : 0);

        return $this;
    }

    /**
     * Use automatic x offset
     *
     * @param bool $usePageOffset
     *
     * @return GoToPage
     * @throws \Exception
     */
    public function setUsePageOffset(bool $usePageOffset): GoToPage
    {
        $this->setParam('usepageoffset', $usePageOffset ? 1 : 0);

        return $this;
    }

    /**
     * Set Double Pages
     *
     * @param bool $useDoublePage
     *
     * @return GoToPage
     * @throws \Exception
     */
    public function setDoublePage(bool $useDoublePage): GoToPage
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
    public function buildCommand(bool $addCmd = true): array
    {
        $return = parent::buildCommand($addCmd);
        $this->getCommandQueue()
             ->setPageNumber($this->getParam('pagenumber'));

        return $return;
    }
}
