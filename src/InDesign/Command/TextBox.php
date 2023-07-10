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

use Mds\PimPrint\CoreBundle\InDesign\Command\Traits\DefaultLocalizedParamsTrait;
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
    use DefaultLocalizedParamsTrait;

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
     * Resizes the frame, so it fits the content height but keeps width.
     *
     * @var int
     */
    const FIT_FRAME_TO_CONTENT_HEIGHT = 2;


    /**
     * Array with all allowed fits for validation.
     *
     * @var array
     */
    protected array $allowedFits = [
        self::FIT_NO_ADJUST,
        self::FIT_FRAME_TO_CONTENT,
        self::FIT_FRAME_TO_CONTENT_HEIGHT
    ];

    /**
     * Available command params with default values.
     *
     * @var array
     */
    private array $availableParams = [
        'values' => [],
        'fit'    => self::FIT_NO_ADJUST,
    ];

    /**
     * TextBox constructor.
     *
     * @param string     $elementName
     * @param float|null $left   Left position in mm.
     * @param float|null $top    Top position in mm.
     * @param float|null $width  Width of element in mm.
     * @param float|null $height Height of element in mm.
     * @param int        $fit    Fit mode of image in image-box. Use FIT class constants.
     *
     * @throws \Exception
     */
    public function __construct(
        string $elementName = '',
        float $left = null,
        float $top = null,
        float $width = null,
        float $height = null,
        int $fit = self::FIT_NO_ADJUST
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
        $this->initLocalizedParams();
    }

    /**
     * Sets the InDesign fit mode of the image.
     *
     * @param int $fit Fit mode of text box. Use FIT class constants.
     *
     * @return TextBox
     * @throws \Exception
     */
    public function setFit(int $fit): TextBox
    {
        $this->setParam('fit', $fit);

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
    public function addString(string $string): TextBox
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
    public function addText(Text $text): TextBox
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
    public function addParagraph(Paragraph $paragraph): TextBox
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
    public function clearContent(): TextBox
    {
        $this->params['values'] = [];
        $this->collectedImages = [];

        return $this;
    }
}
