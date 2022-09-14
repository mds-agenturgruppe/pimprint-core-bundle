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
 * Class RemoveEmptyLayers
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command
 */
class RemoveEmptyLayers extends ExecuteScript
{
    /**
     * ExecuteScript constructor.
     *
     * @param string $script
     *
     * @throws \Exception
     */
    public function __construct(string $script = '')
    {
        $script = 'var currentLayers = PimPrintDocument.currentDoc.layers;
            for(var i=currentLayers.length-1;i>=0;i--)
            {
                  if(currentLayers[i].pageItems.length == 0)
                  { try { currentLayers[i].remove()}catch ( e ) {} }
            };';
        parent::__construct($script);
    }
}
