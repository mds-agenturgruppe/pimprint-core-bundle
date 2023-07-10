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
use Mds\PimPrint\CoreBundle\InDesign\Text\ParagraphComponent;
use Mds\PimPrint\CoreBundle\Project\Traits\ProjectAwareTrait;

/**
 * Class FileBoxCommand
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command
 */
class FileBox extends AbstractBox implements ParagraphComponent
{
    use FitTrait;
    use ProjectAwareTrait;
    use DefaultLocalizedParamsTrait;

    /**
     * Command name.
     *
     * @var string
     */
    const CMD = 'pbox';

    /**
     * Centers content in the frame; preserves the frame size as well as content size and proportions.
     * Note: If the content is larger than the frame, content around the edges is obscured.
     *
     * @see https://www.indesignjs.de/extendscriptAPI/indesign-latest/#FitOptions.html
     * @var string
     */
    const FIT_CENTER_CONTENT = 'CENTER_CONTENT';

    /**
     * Selects best crop region of the content for the frame based on Adobe Sensei.
     * Note: Preserves frame size but might scale the content size.
     *
     * @see https://www.indesignjs.de/extendscriptAPI/indesign-latest/#FitOptions.html
     * @var string
     */
    const FIT_CONTENT_AWARE_FIT = 'CONTENT_AWARE_FIT';

    /**
     * Resizes content to fit the frame.
     * Note: Content that is a different size than the frame appears stretched or squeezed.
     *
     * @see https://www.indesignjs.de/extendscriptAPI/indesign-latest/#FitOptions.html
     * @var string
     */
    const FIT_CONTENT_TO_FRAME = 'CONTENT_TO_FRAME';

    /**
     * Resizes content to fill the frame while perserving the proportions of the content.
     * If the content and frame have different proportions, some of the content is obscured by
     * the bounding box of the frame.
     *
     * @see https://www.indesignjs.de/extendscriptAPI/indesign-latest/#FitOptions.html
     * @var string
     */
    const FIT_FILL_PROPORTIONALLY = 'FILL_PROPORTIONALLY';

    /**
     * Resizes the frame so it fits the content.
     *
     * @see https://www.indesignjs.de/extendscriptAPI/indesign-latest/#FitOptions.html
     * @var string
     */
    const FIT_FRAME_TO_CONTENT = 'FRAME_TO_CONTENT';

    /**
     * Resizes content to fit the frame while preserving content proportions.
     * If the content and frame have different proportions, some empty space appears in the frame.
     *
     * @see https://www.indesignjs.de/extendscriptAPI/indesign-latest/#FitOptions.html
     * @var string
     */
    const FIT_PROPORTIONALLY = 'PROPORTIONALLY';

    /**
     * Asset property name with Model\Asset to use for placement in InDesign.
     * Property can optionally be assigned to assets in Pimcore to explicitly set the used asset for PimPrint.
     *
     * @var string
     */
    const PROPERTY_PIMPRINT_ASSET = 'pimprint_asset';

    /**
     * Array with all allowed fits for validation.
     *
     * @var array
     */
    protected array $allowedFits = [
        self::FIT_PROPORTIONALLY,
        self::FIT_FILL_PROPORTIONALLY,
        self::FIT_CONTENT_TO_FRAME,
        self::FIT_CENTER_CONTENT,
        self::FIT_CONTENT_AWARE_FIT,
        self::FIT_FRAME_TO_CONTENT
    ];

    /**
     * Available command params with default values.
     *
     * @var array
     */
    private array $availableParams = [
        'fit'          => self::FIT_PROPORTIONALLY,
        'src'          => '',
        'assetId'      => '',
        'mtime'        => '',
        'thumbnailUrl' => '',
        'srcUrl'       => '',
    ];

    /**
     * FileBox constructor.
     *
     * @param string      $elementName Name of template element.
     * @param float|null  $left        Left position in mm.
     * @param float|null  $top         Top position in mm.
     * @param float|null  $width       Width of element in mm.
     * @param float|null  $height      Height of element in mm.
     * @param string|null $src         Relative file path from the plugin image directory to the file to be placed.
     * @param string      $fit         Fit mode of image in image-box. Use FIT class constants.
     *
     * @throws \Exception
     */
    public function __construct(
        string $elementName = '',
        float $left = null,
        float $top = null,
        float $width = null,
        float $height = null,
        string $src = null,
        string $fit = self::FIT_PROPORTIONALLY
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

        if (!empty($src)) {
            $this->setSrc($src);
        }

        $this->initLocalizedParams();
    }

    /**
     * Sets relative file path from the plugin image directory to the file to be placed.
     *
     * @param string $src
     *
     * @return FileBox
     * @throws \Exception
     */
    public function setSrc(string $src): FileBox
    {
        $this->setParam('src', $src);

        return $this;
    }
}
