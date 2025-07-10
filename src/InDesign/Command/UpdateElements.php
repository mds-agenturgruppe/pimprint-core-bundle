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
     * Available command params with default values.
     *
     * @var array
     */
    private array $availableParams = [
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
