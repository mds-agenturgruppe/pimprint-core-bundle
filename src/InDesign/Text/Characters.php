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

/**
 * Class Characters
 *
 * Allowed spacial characters inside text content:
 * https://www.indesignjs.de/extendscriptAPI/indesign-latest/#SpecialCharacters.html
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Text
 */
class Characters implements ParagraphComponent
{
    /**
     * InDesign character style.
     *
     * @var string
     */
    protected $style = '';

    /**
     * Text content.
     *
     * @var string
     */
    protected $text;

    /**
     * Hyperlink url.
     *
     * @var string
     */
    protected $href = null;

    /**
     * Hyperlink target.
     *
     * @var string
     */
    protected $target = '_blank';

    /**
     * Marker in InDesign to create interlinking inside the Document
     * Example 'cross_reference'
     *
     * @var string
     * @todo Implement Markers and CrossReference
     */
    protected $marker;

    /**
     * Characters constructor.
     *
     * @param string|null $text
     * @param string|null $style
     */
    public function __construct(string $text = null, string $style = null)
    {
        if (null !== $style) {
            $this->setStyle($style);
        }
        if (null !== $text) {
            $this->setText($text);
        }
    }

    /**
     * Sets character style.
     *
     * @param string $style
     *
     * @return Characters
     */
    public function setStyle(string $style): Characters
    {
        $this->style = $style;

        return $this;
    }

    /**
     * Sets text.
     *
     * @param string $text
     *
     * @return Characters
     */
    public function setText(string $text): Characters
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Returns text.
     *
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * Sets hyperlink of characters.
     *
     * @param string $href
     *
     * @return Characters
     */
    public function setHref(string $href): Characters
    {
        $this->href = $href;

        return $this;
    }

    /**
     * Sets target of hyperlink.
     *
     * @param string $target
     *
     * @return Characters
     */
    public function setTarget(string $target): Characters
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Builds characters command.
     *
     * @return array
     */
    public function buildCommand()
    {
        $command = [
            'cs'     => $this->style,
            'text'   => $this->text,
            'href'   => $this->href,
            'target' => $this->target,
        ];

        if (empty($command['href'])) {
            unset($command['target']);
        }

        return array_filter($command);
    }
}
