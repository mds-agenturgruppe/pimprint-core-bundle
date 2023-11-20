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

use League\Flysystem\FilesystemException;
use Mds\PimPrint\CoreBundle\InDesign\Command\Traits\FitTrait;
use Mds\PimPrint\CoreBundle\InDesign\Command\Traits\ImageCollectorTrait;
use Mds\PimPrint\CoreBundle\InDesign\Text\ParagraphComponent;
use Mds\PimPrint\CoreBundle\InDesign\Traits\MissingAssetNotifierTrait;
use Pimcore\Model\Asset;
use Pimcore\Model\Asset\Document as DocumentAsset;
use Pimcore\Model\Asset\Image as ImageAsset;
use Pimcore\Tool\Storage;

/**
 * Class ImageBox
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command
 */
class ImageBox extends AbstractBox implements ParagraphComponent, ImageCollectorInterface
{
    use FitTrait;
    use ImageCollectorTrait;
    use MissingAssetNotifierTrait;

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
     * Allowed MIME types for Asset.
     *
     * @var array
     */
    private array $allowedMimeTypes = [
        'image/bmp',
        'image/jpeg',
        'image/png',
        'image/tiff',
        'image/vnd.adobe.photoshop',
        'image/x-photoshop',
        'image/x-eps',
        'application/x-photoshop',
        'application/photoshop',
        'application/psd',
        'image/psd',
        'application/postscript',
        'application/pdf',
        'application/ai',
    ];

    /**
     * CopyBox constructor.
     *
     * @param string         $elementName Name of template element.
     * @param float|int|null $left        Left position in mm.
     * @param float|int|null $top         Top position in mm.
     * @param float|int|null $width       Width of element in mm.
     * @param float|int|null $height      Height of element in mm.
     * @param Asset|null     $asset       Asset to be placed.
     * @param string         $fit         Fit mode of image in image-box. Use FIT class constants.
     *
     * @throws \Exception|FilesystemException
     */
    public function __construct(
        string $elementName = '',
        float|int $left = null,
        float|int $top = null,
        float|int $width = null,
        float|int $height = null,
        Asset $asset = null,
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

        if ($asset instanceof Asset) {
            $this->setAsset($asset);
        }
    }

    /**
     * Sets $asset as placed Image in InDesign.
     * if $thumbnailName is set, the generated thumbnail will be used as image in InDesign.
     * If $resize is true, the box is automatically resized to the actual size of the image.
     *
     * @param Asset       $asset
     * @param string|null $thumbnailName
     * @param bool        $resize
     * @param array       $defaultDpi
     *
     * @return ImageBox
     * @throws \Exception|FilesystemException
     */
    public function setAsset(
        Asset $asset,
        string $thumbnailName = null,
        bool $resize = false,
        array $defaultDpi = [300, 300]
    ): static {
        $fallback = $asset->getProperty(self::PROPERTY_PIMPRINT_ASSET);
        if ($fallback instanceof Asset) {
            $asset = $fallback;
            $thumbnailName = null;
        }
        if (null !== $thumbnailName && false === $this->getProject()
                                                      ->config()
                                                      ->isAssetDownloadEnabled()) {
            throw new \Exception(
                'Usage of asset thumbnails is only possible when asset download is enabled for project.',
                $asset->getId()
            );
        }
        $this->assureValidAsset($asset, $thumbnailName);
        $this->addDownloadParams($asset, $thumbnailName);
        if (true === $resize && $asset instanceof ImageAsset) {
            $sizes = $this->getProject()
                          ->imageDimensions()
                          ->getSizes($asset, $defaultDpi);
            $this->setWidth($sizes['width_mm'])
                 ->setHeight($sizes['height_mm']);
        }

        return $this;
    }

    /**
     * Assures that $asset usable in InDesign.
     *
     * @param Asset       $asset
     * @param string|null $thumbnailName
     *
     * @throws \Exception
     */
    private function assureValidAsset(Asset $asset, string $thumbnailName = null)
    {
        if (false === $asset instanceof ImageAsset && false === $asset instanceof DocumentAsset) {
            throw new \Exception(
                sprintf(
                    "Invalid asset type '%s' if asset id %s (%s). Only 'Asset\Image' or 'Asset\Document' are allowed.",
                    get_class($asset),
                    $asset->getId(),
                    $asset->getFilename()
                ),
                $asset->getId()
            );
        }
        if (null !== $thumbnailName) {
            return;
        }
        if (false === in_array($asset->getMimetype(), $this->allowedMimeTypes)) {
            throw new \Exception(
                sprintf(
                    "Invalid MIME-type '%s' of asset id %s (%s).",
                    $asset->getMimetype(),
                    $asset->getId(),
                    $asset->getFilename()
                ),
                $asset->getId()
            );
        }
    }

    /**
     * Adds asset download command params.
     *
     * @param Asset       $asset
     * @param string|null $thumbnailName
     *
     * @throws \Exception|FilesystemException
     */
    private function addDownloadParams(Asset $asset, string $thumbnailName = null)
    {
        if (false === $this->getProject()
                           ->config()
                           ->isAssetDownloadEnabled()) {
            return;
        }
        $thumbnailHelper = $this->getProject()
                                ->thumbnailHelper();
        $thumbnailConfig = $thumbnailHelper->getThumbnailConfig($thumbnailName);
        $thumbnail = null;
        if ($asset instanceof ImageAsset) {
            $thumbnail = $asset->getThumbnail($thumbnailConfig);
        } elseif ($asset instanceof DocumentAsset) {
            $thumbnail = $asset->getImageThumbnail($thumbnailConfig);
        }
        if (null === $thumbnail) {
            throw new \Exception(
                sprintf(
                    "Thumbnail '%s' could be create for asset id %s (%s).",
                    $thumbnailName,
                    $asset->getId(),
                    $asset->getFilename()
                ),
                $asset->getId()
            );
        }

        $storage = Storage::get('asset');
        if (false === $storage->fileExists($asset->getRealFullPath())) {
            $this->notifyMissingAsset(
                sprintf("Asset file '%s' not found.", $asset->getFullPath()),
                $asset->getId()
            );

            return;
        }
        $hostUrl = $this->getProject()
                        ->getHostUrl();
        $this->setParam('assetId', $asset->getId());
        if (null === $thumbnailName) {
            $thumbUrl = $thumbnailHelper->replaceNotSupported($thumbnail->getPath(true));
            $this->setParam('src', $asset->getRealFullPath());
            $this->setParam('mtime', $storage->lastModified($asset->getRealFullPath()));
            $this->setParam('srcFileSize', $asset->getFileSize());
            $this->setParam('srcUrl', $hostUrl . urlencode_ignore_slash($asset->getRealFullPath()));
            $this->setParam('thumbnailUrl', $hostUrl . $thumbUrl);
        } else {
            $this->addForcedThumbnailParams($thumbnail);
        }
    }

    /**
     * Adds download params for forced thumbnail.
     *
     * @param ImageAsset\Thumbnail $thumbnail
     *
     * @throws \Exception
     */
    private function addForcedThumbnailParams(ImageAsset\Thumbnail $thumbnail)
    {
        $thumbnailHelper = $this->getProject()
                                ->thumbnailHelper();
        $srcUrl = $thumbnail->getPath(false);
        if ($thumbnailHelper->isNotSupportedImage($srcUrl)) {
            $srcUrl = $thumbnailHelper->replaceNotSupported($srcUrl);
            $fileSize = 259010;
        } else {
            if (false === file_exists($thumbnail->getLocalFile())) {
                $this->notifyMissingAsset(
                    sprintf("Thumbnail '%s' could not be created.", $thumbnail->getLocalFile()),
                    $thumbnail->getAsset()
                              ->getId()
                );

                return;
            }
            $fileSize = $thumbnail->getFileSize();
            $this->setParam('mtime', @filemtime($thumbnail->getLocalFile()));
        }

        $hostUrl = $this->getProject()
                        ->getHostUrl();
        $this->setParam('src', str_replace('%20', ' ', $srcUrl));
        $this->setParam('srcFileSize', $fileSize);
        $this->setParam('srcUrl', $hostUrl . $srcUrl);
        $this->setParam('thumbnailUrl', '');
    }

    /**
     * Builds command array that is sent as JSON to InDesign.
     *
     * @param bool $addCmd
     *
     * @return array
     * @throws \Exception
     */
    public function buildCommand(bool $addCmd = true): array
    {
        $this->collectImage($this);

        return parent::buildCommand($addCmd);
    }
}
