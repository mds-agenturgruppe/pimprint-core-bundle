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
 * Class PageMessage
 *
 * Displays messages in InDesign Plugin bound to the current page.
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command
 */
class PageMessage extends AbstractCommand
{
    /**
     * Command name.
     *
     * @var string
     */
    const CMD = 'pagemessage';

    /**
     * Available command params with default values.
     *
     * @var array
     */
    private $availableParams = [
        'message' => '',
        'onPage'  => false,
    ];

    /**
     * PageMessage constructor.
     *
     * @param string $message Message to display
     * @param bool   $onPage  Display offPage or onPage
     */
    public function __construct(
        $message = '',
        $onPage = false
    ) {
        $this->initParams($this->availableParams);
        $this->setMessage($message);
        $this->setOnPage($onPage);
    }

    /**
     * Sets message to display.
     *
     * @param string $message
     *
     * @return PageMessage
     */
    public function setMessage(string $message)
    {
        try {
            $this->setParam('message', $message);
        } catch (\Exception $e) {
            return $this;
        }

        return $this;
    }

    /**
     * Display message onPage or not.
     *
     * @param bool $onPage
     *
     * @return PageMessage
     */
    public function setOnPage(bool $onPage)
    {
        try {
            $this->setParam('onPage', $onPage);
        } catch (\Exception $e) {
            return $this;
        }

        return $this;
    }
}
