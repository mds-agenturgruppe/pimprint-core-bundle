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
 * Class ExecuteScript
 *
 * Executes JavaScript in InDesign.
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command
 */
class ExecuteScript extends AbstractCommand
{
    /**
     * Command name.
     *
     * @var string
     */
    const CMD = 'eval';

    /**
     * Available command params.
     *
     * @var array
     */
    protected $availableParams = [
        'value' => '',
    ];

    /**
     * ExecuteScript constructor.
     *
     * @param string $script JavaScript to be executed in InDesign.
     *
     * @throws \Exception
     */
    public function __construct($script = '')
    {
        $this->initParams($this->availableParams);

        $this->setScript($script);
    }

    /**
     * Sets $script as JavaScript to be executed in InDesign.
     *
     * @param string $script JavaScript to be executed in InDesign.
     *
     * @return ExecuteScript
     * @throws \Exception
     */
    public function setScript($script)
    {
        $this->setParam('value', $script);

        return $this;
    }

    /**
     * Validates command.
     *
     * @throws \Exception
     */
    protected function validate()
    {
        $script = $this->getParam('value');
        if (empty($script)) {
            throw new \Exception("No JavaScript set in parameter 'script'.");
        }
    }
}
