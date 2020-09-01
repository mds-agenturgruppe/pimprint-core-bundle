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

use Mds\PimPrint\CoreBundle\InDesign\Command\Traits\ElementNameTrait;
use Mds\PimPrint\CoreBundle\InDesign\Command\Traits\LayerTrait;
use Mds\PimPrint\CoreBundle\InDesign\Command\Traits\PositionTrait;
use Mds\PimPrint\CoreBundle\InDesign\Command\Traits\SizeTrait;
use Mds\PimPrint\CoreBundle\InDesign\Command\Traits\VariableTrait;
use Mds\PimPrint\CoreBundle\InDesign\Command\Variable\DependentInterface;

/**
 * Class GroupEnd
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command
 */
class GroupEnd extends AbstractCommand implements DependentInterface
{
    use ElementNameTrait, LayerTrait, PositionTrait, SizeTrait, VariableTrait;

    /**
     * Command name.
     *
     * @var string
     */
    const CMD = 'groupend';

    /**
     * {@inheritDoc}
     *
     * @var array
     */
    protected $availableParams = [
        'moveTo'       => null,
        'ungroupafter' => 0,
        'checknewpage' => null,
    ];

    /**
     * GroupEnd constructor.
     *
     * @param CheckNewPage|null $cmd
     * @param bool              $moveTo
     * @param bool              $ungroupAfter
     *
     * @throws \Exception
     */
    public function __construct(CheckNewPage $cmd = null, bool $moveTo = false, bool $ungroupAfter = false)
    {
        $this->initParams($this->availableParams);
        $this->setNewPageCmd($cmd);
        $this->setMoveTo($moveTo);
        $this->setUngroupAfter($ungroupAfter);
    }

    /**
     * Set CheckNewPage Command.
     *
     * @param CheckNewPage|null $checkNewPage
     *
     * @return GroupEnd
     * @throws \Exception
     */
    public function setNewPageCmd(CheckNewPage $checkNewPage = null)
    {
        if (null === $checkNewPage) {
            $this->removeParam('checknewpage');
        } else {
            $this->setParam('checknewpage', $checkNewPage->buildCommand());
        }

        return $this;
    }

    /**
     * Sets ungroup after postion calculate.
     *
     * @param bool $ungroupAfter
     *
     * @return GroupEnd
     * @throws \Exception
     */
    public function setUngroupAfter(bool $ungroupAfter)
    {
        $this->setParam('ungroupafter', $ungroupAfter ? 1 : 0);

        return $this;
    }

    /**
     * Sets ungroup after postion calculate.
     *
     * @param bool $moveTo
     *
     * @return GroupEnd
     * @throws \Exception
     */
    public function setMoveTo(bool $moveTo)
    {
        $this->setParam('moveTo', $moveTo ? 1 : 0);

        return $this;
    }
}
