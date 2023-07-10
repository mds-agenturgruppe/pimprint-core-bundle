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
 * Command to sort layers in generated document
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command
 */
class SortLayers extends AbstractCommand
{
    /**
     * Command name.
     *
     * @var string
     */
    const CMD = 'sortLayers';

    /**
     * Available command params with default values.
     *
     * @var array
     */
    protected array $availableParams = [
        'layerOrder' => [],
    ];

    /**
     * SortLayers constructor
     *
     * @param array $layerOrder
     *
     * @throws \Exception
     */
    public function __construct(array $layerOrder = [])
    {
        $this->initParams($this->availableParams);

        $this->setOrder($layerOrder);
    }

    /**
     * Sets $layerOrder for ordering layers.
     * Layer names can be exact layer names or regEx patterns. Patterns must be enclosed by "/".
     *
     * @param array $layerOrder
     *
     * @return SortLayers
     * @throws \Exception
     */
    public function setOrder(array $layerOrder): SortLayers
    {
        $this->setParam('layerOrder', $layerOrder);

        return $this;
    }
}
