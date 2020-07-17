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

namespace Mds\PimPrint\CoreBundle\InDesign;

use Mds\PimPrint\CoreBundle\InDesign\Command\ImageCollectorInterface;
use Mds\PimPrint\CoreBundle\InDesign\Command\Traits\ImageCollectorTrait;
use Mds\PimPrint\CoreBundle\InDesign\Html\TextParser;
use Mds\PimPrint\CoreBundle\InDesign\Html\Style;
use Mds\PimPrint\CoreBundle\InDesign\Text\Paragraph;

/**
 * Class Text
 *
 * @package Mds\PimPrint\CoreBundle\Indesign
 */
class Text implements ImageCollectorInterface
{
    use ImageCollectorTrait;

    /**
     * Paragraphs of text.
     *
     * @var Paragraph[]
     */
    protected $paragraphs = [];

    /**
     * Default paragraph style when adding text.
     *
     * @var string
     */
    protected $paragraphStyle = '';

    /**
     * Default character style when adding text.
     *
     * @var string
     */
    protected $characterStyle = '';

    /**
     * Internal helper.
     *
     * @var bool
     */
    protected $skipDetection = false;

    /**
     * HTML parser instance.
     *
     * @var TextParser
     */
    protected $htmlParser;

    /**
     * Text constructor.
     *
     * @param string|null $paragraphStyle
     * @param string|null $characterStyle
     */
    public function __construct(string $paragraphStyle = null, string $characterStyle = null)
    {
        if (null !== $paragraphStyle) {
            $this->setParagraphStyle($paragraphStyle);
        }
        if (null !== $characterStyle) {
            $this->setCharacterStyle($characterStyle);
        }
    }

    /**
     * Sets default paragraph style.
     *
     * @param string $paragraphStyle
     *
     * @return Text
     */

    public function setParagraphStyle(string $paragraphStyle)
    {
        $this->paragraphStyle = $paragraphStyle;

        return $this;
    }

    /**
     * Sets default character style.
     *
     * @param string $characterStyle
     *
     * @return Text
     */
    public function setCharacterStyle(string $characterStyle)
    {
        $this->characterStyle = $characterStyle;

        return $this;
    }

    /**
     * Clears all content.
     *
     * @return Text
     */
    public function clear()
    {
        $this->paragraphs = [];
        $this->collectedImages = [];

        return $this;
    }

    /**
     * Adds $paragraph.
     *
     * @param Paragraph $paragraph
     *
     * @return Text
     */
    public function addParagraph(Paragraph $paragraph)
    {
        $this->paragraphs[] = $paragraph;

        return $this;
    }

    /**
     * Returns all paragraphs.
     *
     * @return Paragraph[]
     */
    public function getParagraphs(): array
    {
        return $this->paragraphs;
    }

    /**
     * Convenience method to transform $string to Text.
     * If $string looks like HTML addHtml() is used. Otherwise $string is handled as plain text.
     *
     * @param string      $string
     * @param string|null $paragraphStyle
     * @param string|null $characterStyle
     *
     * @return Text
     * @throws \Exception
     */
    public function addString(string $string, string $paragraphStyle = null, string $characterStyle = null): Text
    {
        $this->skipDetection = true;
        if (true === $this->isStringHtml($string)) {
            $this->addHtml($string);
        } else {
            $this->addPlainText($string, $paragraphStyle, $characterStyle);
        }
        $this->skipDetection = false;

        return $this;
    }

    /**
     * Returns true if $string might be HTML.
     *
     * @param string $string
     *
     * @return bool
     */
    protected function isStringHtml(string $string): bool
    {
        if ($string == strip_tags($string)) {
            return false;
        }

        return true;
    }

    /**
     * Adds $string as plaintext paragraph to intance.
     *
     * @param string      $string
     * @param string|null $paragraphStyle
     * @param string|null $characterStyle
     *
     * @return Text
     */
    public function addPlainText($string, string $paragraphStyle = null, string $characterStyle = null)
    {
        if ($this->skipDetection) {
            if (true === $this->isStringHtml($string)) {
                return $this->addPlainText($string);
            }
        }
        $texts = preg_split('#[\r\n]\s*[\r\n]#', $string);
        foreach ($texts as $text) {
            $this->paragraphs[] = new Paragraph(
                $text,
                false === empty($paragraphStyle) ? $paragraphStyle : $this->paragraphStyle,
                false === empty($characterStyle) ? $characterStyle : $this->characterStyle
            );
        }

        return $this;
    }

    /**
     * Parses $html and adds content as Paragraphs to instance.
     *
     * @param string     $html  HTML string to add to Text instance.
     * @param Style|null $style Optional HTML Style applied to text.
     *
     * @return Text
     * @throws \Exception
     */
    public function addHtml($html, Style $style = null)
    {
        if ($this->skipDetection) {
            if (false === $this->isStringHtml($html)) {
                return $this->addPlainText($html);
            }
        }
        $this->getHtmlParser()
             ->setText($this)
             ->parse($html, $style);

        return $this;
    }

    /**
     * Returns html parser instance.
     * If none is set new parser instance is created by the factory.
     *
     * @return TextParser
     */
    public function getHtmlParser()
    {
        if (null === $this->htmlParser) {
            $this->htmlParser = $this->parserFactory();
        }

        return $this->htmlParser;
    }

    /**
     * Sets HTML parser instance.
     *
     * @param TextParser $parser
     *
     * @return Text
     */
    public function setHtmlParser(TextParser $parser)
    {
        $this->htmlParser = $parser;

        return $this;
    }

    /**
     * Html parser factory.
     * Factory can be overwritten to have project specific parsers created automatically.
     *
     * @return TextParser
     */
    protected function parserFactory()
    {
        return new TextParser();
    }

    /**
     * Builds array that is sent as content parameter in TextBox commands to InDesign.
     *
     * @return array
     * @throws \Exception
     */
    public function buildCommand()
    {
        $return = [];

        foreach ($this->paragraphs as $paragraph) {
            $return[] = $paragraph->buildCommand();
            $this->addCollectedImages($paragraph);
        }

        return $return;
    }
}
