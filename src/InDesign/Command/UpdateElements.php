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
 * Class UpdateElements
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command
 */
class UpdateElements extends AbstractCommand
{
    /**
     * Command name.
     *
     * @var string
     */
    const CMD = 'updateitems';

    /**
     * {@inheritDoc}
     *
     * @var array
     */
    private $availableParams = [
        'list' => [],
    ];

    /**
     * UpdateElements constructor.
     *
     * @param array $elements Updates elements
     *
     * @throws \Exception
     */
    public function __construct(array $elements = [])
    {
        $this->initParams($this->availableParams);
        $this->setElements($elements);
    }

    /**
     * Sets updated elements.
     *
     * @param array $elements
     *
     * @throws \Exception
     */
    public function setElements(array $elements)
    {
        $this->setParam('list', $elements);
    }
}
