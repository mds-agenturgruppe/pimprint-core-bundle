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

use Mds\PimPrint\CoreBundle\InDesign\Command\Traits\LayerTrait;
use Mds\PimPrint\CoreBundle\InDesign\Command\Traits\ElementNameTrait;
use Mds\PimPrint\CoreBundle\InDesign\Command\Traits\PositionTrait;
use Mds\PimPrint\CoreBundle\InDesign\Command\Traits\SizeTrait;
use Mds\PimPrint\CoreBundle\InDesign\Command\Traits\VariableTrait;
use Mds\PimPrint\CoreBundle\InDesign\Command\Variable\DependentInterface;

/**
 * Class AbstractBox
 *
 * Abstract command with generic functionality for box placement commands.
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command
 */
abstract class AbstractBox extends AbstractCommand implements DependentInterface
{
    use ElementNameTrait, LayerTrait, PositionTrait, SizeTrait, VariableTrait;

    /**
     * No resize.
     *
     * @var int
     */
    const RESIZE_NO_RESIZE = 0;

    /**
     * Resize width and height.
     *
     * @var int
     */
    const RESIZE_WIDTH_HEIGHT = 1;

    /**
     * Resize width.
     *
     * @var int
     */
    const RESIZE_WIDTH = 2;

    /**
     * Resize height.
     *
     * @var int
     */
    const RESIZE_HEIGHT = 3;

    /**
     * {@inheritDoc}
     *
     * @var array
     */
    private $availableParams = [
        'tid' => null,
        'cmdfilter'  => null, //currently not used.
    ];

    /**
     * Availible resize values.
     *
     * @var array
     */
    protected $availibleResizes = [
        self::RESIZE_NO_RESIZE,
        self::RESIZE_WIDTH_HEIGHT,
        self::RESIZE_WIDTH,
        self::RESIZE_HEIGHT,
    ];

    /**
     * Initializes abstract box.
     */
    protected function initBoxParams()
    {
        $this->initParams($this->availableParams);
    }

    /**
     * Sets box ident 'tid' parameter.
     *
     * @param string $ident
     *
     * @return AbstractBox
     * @throws \Exception
     */
    public function setBoxIdent($ident)
    {
        $this->setParam('tid', $ident);

        return $this;
    }

    /**
     * Sets cmdfilter for autocommands.
     *
     * @param string $filter
     *
     * @return AbstractBox
     * @throws \Exception
     */
    public function setCmdFilter($filter)
    {
        $this->setParam('cmdfilter', $filter);

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Exception
     */
    protected function validate()
    {
        $this->validateElementNameParam();
        $this->setAutoResize();
    }
}
