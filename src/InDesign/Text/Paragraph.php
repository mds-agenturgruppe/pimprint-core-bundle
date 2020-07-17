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

namespace Mds\PimPrint\CoreBundle\InDesign\Text;

use Mds\PimPrint\CoreBundle\InDesign\Command\ImageBox;
use Mds\PimPrint\CoreBundle\InDesign\Command\ImageCollectorInterface;
use Mds\PimPrint\CoreBundle\InDesign\Command\Traits\ImageCollectorTrait;
use Mds\PimPrint\CoreBundle\InDesign\Traits\BoxIdentBuilderTrait;
use Mds\PimPrint\CoreBundle\Service\SpecialChars;

/**
 * Class Paragraph
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Text
 */
class Paragraph implements ImageCollectorInterface
{
    use BoxIdentBuilderTrait;
    use ImageCollectorTrait;

    /**
     * Prefix for BoxIdent.
     *
     * @var string
     */
    const TID_PREFIX = 'P';

    /**
     * InDesign paragraph style to apply to text.
     *
     * @var string
     */
    protected $paragraphStyle = '';

    /**
     * InDesign character style to apply to text.
     *
     * @var string
     */
    protected $characterStyle = '';

    /**
     * Components in paragraph.
     *
     * @var ParagraphComponent[]
     */
    protected $components;

    /**
     * Paragraph constructor.
     *
     * @param string|null $text
     * @param string|null $paragraphStyle
     * @param string|null $characterStyle
     *
     * @throws \Exception
     */
    public function __construct(string $text = null, string $paragraphStyle = null, string $characterStyle = null)
    {
        if (null !== $paragraphStyle) {
            $this->setParagraphStyle($paragraphStyle);
        }
        if (null !== $characterStyle) {
            $this->setCharacterStyle($characterStyle);
        }
        if (false === empty($text)) {
            $this->addText($text);
        }
    }

    /**
     * Sets InDesign paragraph style.
     *
     * @param string $paragraphStyle
     *
     * @return Paragraph
     */
    public function setParagraphStyle(string $paragraphStyle): Paragraph
    {
        $this->paragraphStyle = $paragraphStyle;

        return $this;
    }

    /**
     * Sets InDesign character style.
     *
     * @param string $characterStyle
     *
     * @return Paragraph
     */
    public function setCharacterStyle(string $characterStyle): Paragraph
    {
        $this->characterStyle = $characterStyle;

        return $this;
    }

    /**
     * Clears all characters in paragraph.
     *
     * @return Paragraph
     */
    public function clear(): Paragraph
    {
        $this->components = [];

        return $this;
    }

    /**
     * Adds $component to paragraph.
     * If $prependSpace is true a SpecialChars::SPACE will be pretended to textual components.
     *
     * @param ParagraphComponent $component
     * @param bool               $prependSpace
     *
     * @return Paragraph
     * @throws \Exception
     */
    public function addComponent(ParagraphComponent $component, bool $prependSpace = false): Paragraph
    {
        if ($component instanceof Characters && $prependSpace) {
            $component->setText(
                $this->getProject()
                     ->specialChars()
                     ->utf8(SpecialChars::SPACE) . $component->getText()
            );
        }
        $this->components[] = $component;

        return $this;
    }

    /**
     * Convenience method that clears all components in paragraph and adds $text.
     *
     * @param string      $text
     * @param string|null $style
     *
     * @return Paragraph
     * @throws \Exception
     */
    public function setText(string $text, string $style = null): Paragraph
    {
        $this->clear();
        $this->addText($text, $style);

        return $this;
    }

    /**
     * Adds $text as characters with $style. If style is null characterStyle from paragraph is used.
     *
     * @param string      $text
     * @param string|null $style
     *
     * @return Paragraph
     * @throws \Exception
     */
    public function addText(string $text, string $style = null): Paragraph
    {
        if (null === $style) {
            $style = $this->characterStyle;
        }
        $this->addComponent(
            new Characters($text, $style)
        );

        return $this;
    }

    /**
     * Builds array that is sent as content parameter in TextBox commands to InDesign.
     *
     * @return array
     * @throws \Exception
     */
    public function buildCommand()
    {
        $array = [
            'ps'    => $this->paragraphStyle,
            'chars' => []
        ];
        if (empty($this->components)) {
            return $array;
        }
        foreach ($this->components as $component) {
            if ($component instanceof ImageBox) {
                $this->createBoxIdent($component);
                $imageCommand = $component->buildCommand();
                $this->addCollectedImages($component);
                $command = [
                    ImageBox::CMD => $imageCommand,
                ];
            } else {
                $command = $component->buildCommand();
            }
            $array['chars'][] = $command;
        }

        return $array;
    }
}
