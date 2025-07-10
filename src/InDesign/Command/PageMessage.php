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
    private array $availableParams = [
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
        string $message = '',
        bool $onPage = false
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
    public function setMessage(string $message): PageMessage
    {
        try {
            $this->setParam('message', $message);
        } catch (\Exception) {
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
    public function setOnPage(bool $onPage): PageMessage
    {
        try {
            $this->setParam('onPage', $onPage);
        } catch (\Exception) {
            return $this;
        }

        return $this;
    }
}
