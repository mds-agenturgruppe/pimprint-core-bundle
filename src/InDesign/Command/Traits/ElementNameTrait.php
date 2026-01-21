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

namespace Mds\PimPrint\CoreBundle\InDesign\Command\Traits;

use Mds\PimPrint\CoreBundle\InDesign\Command\AbstractBox;

/**
 * Trait to add name param to a command.
 * The name param is used to identify elements by name in the template document.
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command\Traits
 */
trait ElementNameTrait
{
    /**
     * Initializes trait
     *
     * @return void
     */
    protected function initElementName(): void
    {
        $this->initParams(['name' => '']);
    }

    /**
     * Sets $name as name for the element to be copied from the template document.
     *
     * @param string $elementName
     *
     * @return ElementNameTrait|AbstractBox
     * @throws \Exception
     */
    public function setElementName(string $elementName): AbstractBox|static
    {
        $this->setParam('name', $elementName);

        return $this;
    }

    /**
     * Returns the name of the element copied from the template document.
     *
     * @return string
     */
    public function getElementName(): string
    {
        try {
            return $this->getParam('name');
        } catch (\Exception) {
            return '';
        }
    }

    /**
     * Validates of name param is set in command.
     *
     * @return void
     * @throws \Exception
     */
    protected function validateElementNameParam(): void
    {
        $this->validateEmptyParam('name', 'setElementName');
    }
}
