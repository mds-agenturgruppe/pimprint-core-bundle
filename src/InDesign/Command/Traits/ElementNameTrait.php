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

namespace Mds\PimPrint\CoreBundle\InDesign\Command\Traits;

use Mds\PimPrint\CoreBundle\InDesign\Command\AbstractBox;
use Mds\PimPrint\CoreBundle\InDesign\Command\AbstractCommand;

/**
 * Trait ElementNameTrait
 *
 * Trait to add name param to a command.
 * The name param is used to identify elements by name in the template document.
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command\Traits
 */
trait ElementNameTrait
{
    /**
     * Initializes trait
     */
    protected function initElementName()
    {
        $this->initParams(['name' => '']);
    }

    /**
     * Sets $name as name for the element to be copied from the template document.
     *
     * @param string $elementName
     *
     * @return ElementNameTrait|AbstractBox
     */
    public function setElementName(string $elementName)
    {
        $this->setParam('name', $elementName);

        return $this;
    }

    /**
     * Validates of name param is set in command.
     *
     * @throws \Exception
     */
    protected function validateElementNameParam()
    {
        $this->validateEmptyParam('name', 'setElementName');
    }
}
