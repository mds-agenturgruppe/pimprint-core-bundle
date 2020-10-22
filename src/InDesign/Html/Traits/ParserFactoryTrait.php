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

namespace Mds\PimPrint\CoreBundle\InDesign\Html\Traits;

use Mds\PimPrint\CoreBundle\InDesign\Command\ImageBox;
use Mds\PimPrint\CoreBundle\InDesign\Html\Style;
use Mds\PimPrint\CoreBundle\InDesign\Text\Characters;
use Mds\PimPrint\CoreBundle\InDesign\Text\Paragraph;
use Pimcore\Model\Asset;

/**
 * Trait ParserFactoryTrait
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Html\Traits
 */
trait ParserFactoryTrait
{
    /**
     * Closure used for creating commands.
     *
     * @var \Closure
     */
    protected $factoryClosure;

    /**
     * Sets $commandCreator Closure. If $commandCreator is null a empty closure is created.
     *
     * @param \Closure|null $factoryClosure
     */
    public function setFactoryClosure(\Closure $factoryClosure = null)
    {
        $this->factoryClosure = $factoryClosure;
        if (null === $this->factoryClosure) {
            $this->factoryClosure = function (string $element, \DomElement $node = null) {
                return null;
            };
        }
    }

    /**
     * Factory template method to create Style instance.
     * Project specific instances can be integrated by overwriting method or using factoryClosure
     *
     * @return Style
     */
    protected function styleFactory(): Style
    {
        $style = $this->factoryClosure->call($this, self::FACTORY_ELEMENT_STYLE);
        if ($style instanceof Style) {
            return $style;
        }

        return new Style();
    }

    /**
     * Factory template method for Characters.
     * Project specific instances can be integrated by overwriting method or using factoryClosure
     *
     * @return Characters
     */
    protected function charactersFactory(): Characters
    {
        $characters = $this->factoryClosure->call($this, self::FACTORY_ELEMENT_CHARACTERS);
        if ($characters instanceof Characters) {
            return $characters;
        }

        return new Characters();
    }

    /**
     * Factory template method for Paragraph.
     * Project specific instances can be integrated by overwriting method or using factoryClosure
     *
     * @return Paragraph
     */
    protected function paragraphFactory(): Paragraph
    {
        $paragraph = $this->factoryClosure->call($this, self::FACTORY_ELEMENT_PARAGRAPH);
        if ($paragraph instanceof Paragraph) {
            return $paragraph;
        }

        return new Paragraph();
    }

    /**
     * Factory template method for ImageBox.
     * Project specific instances can be integrated by overwriting method or using factoryClosure
     *
     * @param \DOMElement $node
     * @param Asset       $asset
     *
     * @return ImageBox
     */
    protected function imageFactory(\DOMElement $node, Asset $asset): ImageBox
    {
        $imageBox = $this->factoryClosure->call($this, self::FACTORY_ELEMENT_IMAGE, $node, $asset);
        if ($imageBox instanceof ImageBox) {
            return $imageBox;
        }

        return new ImageBox();
    }
}
