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

namespace Mds\PimPrint\CoreBundle\Project\Traits;

use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\Element\AbstractElement;

/**
 * Trait ElementCollectionRendering
 *
 * @package Mds\PimPrint\DemoBundle\Project\Traits
 */
trait ElementCollectionRenderingTrait
{
    /**
     * Elements to generate pages from.
     *
     * @var array
     */
    private array $elements = [];

    /**
     * Abstract method that collects all elements to render for staring $element.
     *
     * @param AbstractElement|AbstractObject $element
     *
     * @return void
     */
    abstract protected function collectElements(AbstractElement|AbstractObject $element): void;

    /**
     * Abstract method that renders a collected element.
     *
     * @param AbstractElement $element
     *
     * @return void
     */
    abstract protected function renderElement(AbstractElement $element): void;

    /**
     * Returns next element to render.
     *
     * @return mixed
     */
    protected function getNextElement(): mixed
    {
        return array_shift($this->elements);
    }

    /**
     * Renders all publication pages for $element.
     * In concrete Pimcore PimPrint projects starting $elements are usually of type AbstractObject or Document.
     *
     * @param AbstractElement $element
     *
     * @return void
     * @throws \Exception
     */
    protected function renderPages(AbstractElement $element): void
    {
        $this->collectElements($element);
        while (true) {
            $element = $this->getNextElement();
            if (empty($element)) {
                break;
            }
            $this->renderElement($element);
        }
    }
}
