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

use Mds\PimPrint\CoreBundle\InDesign\Command\Traits\FitTrait;
use Mds\PimPrint\CoreBundle\InDesign\Command\Traits\ImageCollectorTrait;
use Mds\PimPrint\CoreBundle\InDesign\Text;
use Mds\PimPrint\CoreBundle\InDesign\Text\Paragraph;

/**
 * Class TextBox
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command
 */
class TextBox extends AbstractBox implements ImageCollectorInterface
{
    use FitTrait;
    use ImageCollectorTrait;

    /**
     * Command name.
     *
     * @var string
     */
    const CMD = 'tbox';

    /**
     * Don't adjust box size.
     *
     * @var int
     */
    const FIT_NO_ADJUST = 0;

    /**
     * Resizes the frame so it fits the content.
     *
     * @see https://www.indesignjs.de/extendscriptAPI/indesign-latest/#FitOptions.html
     * @var int
     */
    const FIT_FRAME_TO_CONTENT = 1;

    /**
     * Resizes the frame so it fits the content but keeps height.
     *
     * @var int
     */
    const FIT_FRAME_TO_CONTENT_HEIGHT = 2;

    /**
     * Array with all allowed fits for validation.
     *
     * @var array
     */
    protected $allowedFits = array(
        self::FIT_NO_ADJUST,
        self::FIT_FRAME_TO_CONTENT,
        self::FIT_FRAME_TO_CONTENT_HEIGHT
    );

    /**
     * Default value for automatic text language layers.
     *
     * @var bool
     */
    protected static $useLanguageLayer = true;

    /**
     * Available command params with default values.
     *
     * @var array
     */
    private $availableParams = [
        'nolnglayer' => null,
        'values'     => [],
        'fit'        => self::FIT_NO_ADJUST,
    ];

    /**
     * TextBox constructor.
     *
     * @param string         $elementName
     * @param float|int|null $left   Left position in mm.
     * @param float|int|null $top    Top position in mm.
     * @param float|int|null $width  Width of element in mm.
     * @param float|int|null $height Height of element in mm.
     * @param int            $fit    Fit mode of image in image-box. Use FIT class constants.
     *
     * @throws \Exception
     */
    public function __construct(
        $elementName = '',
        $left = null,
        $top = null,
        $width = null,
        $height = null,
        $fit = self::FIT_NO_ADJUST
    ) {
        $this->initBoxParams();
        $this->initParams($this->availableParams);

        $this->initElementName();
        $this->initLayer();
        $this->initPosition();
        $this->initSize();

        $this->setElementName($elementName);
        $this->setLeft($left);
        $this->setTop($top);
        $this->setWidth($width);
        $this->setHeight($height);
        $this->setFit($fit);
        $this->setResize(self::RESIZE_WIDTH_HEIGHT);
    }

    /**
     * Sets the InDesign fit mode of the image.
     *
     * @param string $fit Fit mode of text box. Use FIT class constants.
     *
     * @return TextBox
     * @throws \Exception
     */
    public function setFit($fit)
    {
        $this->setParam('fit', $fit);

        return $this;
    }

    /**
     * Sets the default behaviour of language text layers. By default every text box uses this value.
     * The predefined value is true.
     *
     * @param bool $useLanguageLayer
     *
     * @return void
     */
    public static function setDefaultUseLanguageLayer(bool $useLanguageLayer)
    {
        self::$useLanguageLayer = $useLanguageLayer;
    }

    /**
     * Sets if text box should be placed on language layers
     *
     * @param bool $useLanguageLayer
     *
     * @return TextBox
     * @throws \Exception
     */
    public function setUseLanguageLayer(bool $useLanguageLayer)
    {
        $this->setParam(
            'nolnglayer',
            $useLanguageLayer ? 0 : 1
        );

        return $this;
    }

    /**
     * Convenience method to add $string to the Textbox.
     * Internally it creates a Paragraph with no style definitions and adds the paragraph to the textbox.
     *
     * @param string $string
     *
     * @return TextBox
     * @throws \Exception
     */
    public function addString(string $string)
    {
        $this->addParagraph(
            new Paragraph($string)
        );

        return $this;
    }

    /**
     * Sets $content as content of the text box.
     *
     * @param Text $text
     *
     * @return TextBox
     * @throws \Exception
     */
    public function addText(Text $text)
    {
        foreach ($text->getParagraphs() as $paragraph) {
            $this->addParagraph($paragraph);
        }

        return $this;
    }

    /**
     * Adds a text paragraph to the text box.
     *
     * @param Paragraph $paragraph
     *
     * @return TextBox
     * @throws \Exception
     */
    public function addParagraph(Paragraph $paragraph)
    {
        $this->params['values'][] = $paragraph->buildCommand();
        $this->addCollectedImages($paragraph);

        return $this;
    }

    /**
     * Clears content in text box.
     *
     * @return TextBox
     */
    public function clearContent()
    {
        $this->params['values'] = [];
        $this->collectedImages = [];

        return $this;
    }

    /**
     * Builds command array that is sent as JSON to InDesign.
     *
     * @param bool $addCmd
     *
     * @return array
     * @throws \Exception
     */
    public function buildCommand(bool $addCmd = true)
    {
        if (false === isset($this->params['nolnglayer'])) {
            $this->params['nolnglayer'] = self::$useLanguageLayer ? 0 : 1;
        }

        return parent::buildCommand($addCmd);
    }
}
