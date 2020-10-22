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
 * Sets the target layer name for all sequential following commands.
 * Create true:
 * The layer will be created when a element is placed.
 *
 * Create false:
 * If the layer exists the element is placed on this layer. Otherwise the element is placed on the same layer as
 * defined in the template.
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command
 */
class SetLayer extends AbstractCommand
{
    /**
     * Command name.
     *
     * @var string
     */
    const CMD = 'setlayer';

    /**
     * {@inheritDoc}
     *
     * @var array
     */
    protected $availableParams = [
        'name'   => '',
        'create' => 1,
    ];

    /**
     * SetLayer constructor.
     *
     * @param string $layerName
     * @param bool   $create
     *
     * @throws \Exception
     */
    public function __construct(string $layerName, bool $create = true)
    {
        $this->initParams($this->availableParams);

        $this->setLayerName($layerName);
        $this->setCreate($create);
    }

    /**
     * Sets name of layer.
     *
     * @param string $layerName
     *
     * @return SetLayer
     * @throws \Exception
     */
    public function setLayerName(string $layerName): SetLayer
    {
        $this->setParam('name', $layerName);

        return $this;
    }

    /**
     * Sets create param.
     * true: Layer is created and activated in InDesign
     * false: Layer is not created and only activated in InDesign
     *
     * @param bool $create
     *
     * @return SetLayer
     * @throws \Exception
     */
    public function setCreate(bool $create): SetLayer
    {
        $this->setParam('create', $create ? 1 : 0);

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Exception
     */
    protected function validate()
    {
        $this->validateEmptyParam('name', 'setLayerName');
    }
}
