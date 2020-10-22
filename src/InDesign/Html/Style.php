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

namespace Mds\PimPrint\CoreBundle\InDesign\Html;

/**
 * Class Style
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Html
 */
class Style
{
    /**
     * InDesign paragraph style.
     *
     * @var string
     */
    const TYPE_PARAGRAPH = 'paragraph';

    /**
     * InDesign character style.
     *
     * @var string
     */
    const TYPE_CHARACTER = 'character';

    /**
     * InDesign table style.
     *
     * @var string
     */
    const TYPE_TABLE = 'table';

    /**
     * InDesign cell style.
     *
     * @var string
     */
    const TYPE_CELL = 'cell';

    /**
     * Message to show when element is not defined.
     *
     * @var string
     */
    const WARNING_MESSAGE = 'not defined ';

    /**
     * HTML Tag styles definition.
     *
     * @var array
     */
    protected $styles = [];

    /**
     * Toggles warning output for missing styles.
     *
     * @var bool[]
     */
    protected $showWarnings = [
        self::TYPE_PARAGRAPH => false,
        self::TYPE_CHARACTER => false,
        self::TYPE_TABLE     => false,
        self::TYPE_CELL      => false,
    ];

    /**
     * Clears all style definitions.
     *
     * @return Style
     */
    public function clear(): Style
    {
        $this->styles = [];

        return $this;
    }

    /**
     * Sets warnings for undefined Tags.
     *
     * @param bool $character
     * @param bool $paragraph
     * @param bool $table
     * @param bool $cell
     *
     * @return Style
     * @throws \Exception
     */
    public function setWarnings(
        bool $character = false,
        bool $paragraph = false,
        bool $table = false,
        bool $cell = false
    ): Style {
        $this->setWarning(self::TYPE_CHARACTER, $character);
        $this->setWarning(self::TYPE_PARAGRAPH, $paragraph);
        $this->setWarning(self::TYPE_TABLE, $table);
        $this->setWarning(self::TYPE_CELL, $cell);

        return $this;
    }

    /**
     * Sets $warn for style $type.
     *
     * @param string $type
     * @param bool   $warn
     *
     * @return Style
     * @throws \Exception
     */
    public function setWarning($type, bool $warn = false)
    {
        $this->setUpType($type);
        $this->showWarnings[$type] = $warn;

        return $this;
    }

    /**
     * Sets paragraph style for $tag.
     *
     * @param string $tag
     * @param string $style
     *
     * @return Style
     * @throws \Exception
     */
    public function setParagraph(string $tag, string $style): Style
    {
        $this->setTagStyle($tag, $style, self::TYPE_PARAGRAPH);

        return $this;
    }

    /**
     * Returns paragraph style for $tag.
     *
     * @param string $tag
     *
     * @return string
     * @throws \Exception
     */
    public function getParagraph($tag)
    {
        return $this->getStyle($tag, self::TYPE_PARAGRAPH);
    }

    /**
     * Sets character style for $tag.
     *
     * @param string $tag
     * @param string $style
     *
     * @return Style
     * @throws \Exception
     */
    public function setCharacter(string $tag, string $style): Style
    {
        $this->setTagStyle($tag, $style, self::TYPE_CHARACTER);

        return $this;
    }

    /**
     * Returns character style for $tag.
     *
     * @param string $tag
     *
     * @return string
     * @throws \Exception
     */
    public function getCharacter($tag)
    {
        return $this->getStyle($tag, self::TYPE_CHARACTER);
    }

    /**
     * Sets table style.
     *
     * @param string $style
     *
     * @return Style
     * @throws \Exception
     */
    public function setTable(string $style): Style
    {
        $this->setTagStyle('table', $style, self::TYPE_TABLE);

        return $this;
    }

    /**
     * Returns table style.
     *
     * @return string
     * @throws \Exception
     */
    public function getTable()
    {
        return $this->getStyle('table', self::TYPE_TABLE);
    }

    /**
     * Sets cell style for $tag.
     *
     * @param string $tag
     * @param string $style
     *
     * @return Style
     * @throws \Exception
     */
    public function setCell(string $tag, string $style): Style
    {
        $this->setTagStyle($tag, $style, self::TYPE_CELL);

        return $this;
    }

    /**
     * Returns cell style for $tag.
     *
     * @param string $tag
     *
     * @return string
     * @throws \Exception
     */
    public function getCell($tag)
    {
        return $this->getStyle($tag, self::TYPE_CELL);
    }

    /**
     * Sets $paragraphStyle and $characterStyle for $tag.
     *
     * @param string $tag
     * @param string $paragraphStyle
     * @param string $characterStyle
     *
     * @return Style
     * @throws \Exception
     */
    public function setTag($tag, string $paragraphStyle = '', string $characterStyle = ''): Style
    {
        $this->setParagraph($tag, $paragraphStyle)
             ->setCharacter($tag, $characterStyle);

        return $this;
    }

    /**
     * Sets $tag $style for $type
     *
     * @param string $tag   Tag to apply the style
     * @param string $style InDesign style name
     * @param string $type  Typ of style. Use TYPE_ class constants 'paragraph' or 'character'
     *
     * @return Style
     * @throws \Exception
     */
    public function setTagStyle(string $tag, string $style, string $type): Style
    {
        $tag = $this->setUpTag($tag);
        $this->setUpType($type);


        $this->initTag($tag);
        $this->styles[$tag][$type] = $style;

        return $this;
    }

    /**
     * Returns style for $tag and $type.
     *
     * @param string $tag  Tag name
     * @param string $type Typ of style. Use TYPE_ class constants 'paragraph' or 'character'
     *
     * @return string
     * @throws \Exception
     */
    public function getStyle($tag, $type)
    {
        $tag = $this->setUpTag($tag);
        $this->setUpType($type);

        $this->initTag($tag);

        return $this->styles[$tag][$type];
    }

    /**
     * Initializes tag style definition.
     *
     * @param string $tag
     *
     * @return Style
     */
    protected function initTag(string $tag): Style
    {
        if (false === is_array($this->styles[$tag])) {
            $this->styles[$tag] = [];
            foreach ($this->showWarnings as $type => $show) {
                $this->styles[$tag][$type] = $show ? self::WARNING_MESSAGE . $tag : '';
            }
        }

        return $this;
    }

    /**
     * Sets up tag parameter.
     *
     * @param string $tag
     *
     * @return string
     */
    protected function setUpTag(string $tag)
    {
        return strtolower($tag);
    }

    /**
     * Checks if $type is a valid style type.
     * If $type is invalid an exception is thrown.
     *
     * @param string $type
     *
     * @throws \Exception
     */
    protected function setUpType(string $type)
    {
        if (false === isset($this->showWarnings[$type])) {
            throw new \Exception(
                sprintf("Invalid style type '%s'. Use '%s' TYPE_ constants.", $type, Style::class)
            );
        }
    }
}
