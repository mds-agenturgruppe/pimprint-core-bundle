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
use League\Flysystem\FilesystemOperator;
use Mds\PimPrint\CoreBundle\InDesign\Command\Traits\ImageCollectorTrait;
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
class ImageBox extends FileBox implements ImageCollectorInterface
{
    use ImageCollectorTrait;
    use MissingAssetNotifierTrait;

    /**
     * Command name.
     *
     * @var string
     */
    const CMD = 'pbox';

    /**
     * SVG MIME-Type
     *
     * @var string
     */
    const MIME_TYPE_SVG = 'image/svg+xml';

    /**
     * Allowed MIME types for Asset.
     *
     * @var array
     */
    private static array $baseAllowedMimeTypes = [
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
     * Runtime cache
     *
     * @var array
     */
    private static array $allowedMimeTypes;

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
     * CopyBox constructor.
     *
     * @param string     $elementName Name of template element.
     * @param float|null $left        Left position in mm.
     * @param float|null $top         Top position in mm.
     * @param float|null $width       Width of element in mm.
     * @param float|null $height      Height of element in mm.
     * @param Asset|null $asset       Asset to be placed.
     * @param string     $fit         Fit mode of image in image-box. Use FIT class constants.
     *
     * @throws FilesystemException
     * @throws \Exception
     */
    public function __construct(
        string $elementName = '',
        float $left = null,
        float $top = null,
        float $width = null,
        float $height = null,
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

        $this->initLocalizedParams();
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
    ): ImageBox {
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
     * @return void
     * @throws \Exception
     */
    private function assureValidAsset(Asset $asset, string $thumbnailName = null): void
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
        if (false === in_array($asset->getMimetype(), $this->getAllowedMimeTypes())) {
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
     * @return void
     * @throws FilesystemException
     * @throws \Exception
     */
    private function addDownloadParams(Asset $asset, string $thumbnailName = null): void
    {
        if (!$this->getProject()
                  ->config()
                  ->isAssetDownloadEnabled()) {
            return;
        }

        $storage = Storage::get('asset');
        if (false === $storage->fileExists($asset->getRealFullPath())) {
            $this->notifyMissingAsset(
                sprintf("Asset file '%s' not found.", $asset->getFullPath()),
                $asset->getId()
            );

            return;
        }

        $thumbnail = null;
        if ($thumbnailName || !$this->getProject()
                                    ->config()
                                    ->isAssetPreDownloadEnabled()) {
            $thumbnailConfig = $this->getProject()
                                    ->thumbnailHelper()
                                    ->getThumbnailConfig($thumbnailName);
            if ($asset instanceof ImageAsset) {
                $thumbnail = $asset->getThumbnail($thumbnailConfig);
            } elseif ($asset instanceof DocumentAsset) {
                $thumbnail = $asset->getImageThumbnail($thumbnailConfig);
            }
            if (null === $thumbnail) {
                throw new \Exception(
                    sprintf(
                        "Thumbnail '%s' could be create for asset id %s: %s",
                        $thumbnailName,
                        $asset->getId(),
                        $asset->getRealFullPath()
                    ),
                    $asset->getId()
                );
            }
        }

        $this->setParam('assetId', $asset->getId());
        if ($thumbnailName) {
            $this->addForcedThumbnailParams($thumbnail, $asset);

            return;
        }

        $this->addThumbnailParams($asset, $storage, $thumbnail);
    }

    /**
     * Adds download params for $asset
     *
     * @param Asset                                                  $asset
     * @param FilesystemOperator                                     $storage
     * @param ImageAsset\Thumbnail|DocumentAsset\ImageThumbnail|null $thumbnail
     *
     * @return void
     * @throws FilesystemException
     * @throws \Exception
     */
    public function addThumbnailParams(
        Asset $asset,
        FilesystemOperator $storage,
        ImageAsset\Thumbnail|DocumentAsset\ImageThumbnail|null $thumbnail
    ): void {
        $thumbnailHelper = $this->getProject()
                                ->thumbnailHelper();

        $this->setParam('src', $asset->getRealFullPath());
        $this->setParam('srcFileSize', $asset->getFileSize());
        $this->setParam('srcUrl', $thumbnailHelper->prependHostUrl($asset->getFrontendFullPath()));

        if ($this->getProject()
                 ->config()
                 ->offsetGet('file_storage_mtime')) {
            $this->setParam('mtime', $storage->lastModified($asset->getRealFullPath()));
        } else {
            $this->setParam('mtime', (int)$asset->getModificationDate());
        }

        if ($thumbnail) {
            $thumbUrl = $thumbnail->getPath(true);
//            Pimcore >= 10.6
//            $thumbUrl = $thumbnail->getPath(['deferredAllowed' => true, 'frontend' => true]);
            $thumbUrl = $this->getProject()
                             ->thumbnailHelper()
                             ->replaceNotSupported($thumbUrl);
            $this->setParam('thumbnailUrl', $thumbnailHelper->prependHostUrl($thumbUrl));
        }
    }

    /**
     * Adds download params for forced thumbnail.
     *
     * @param ImageAsset\Thumbnail $thumbnail
     * @param Asset                $asset
     *
     * @return void
     * @throws \Exception
     */
    private function addForcedThumbnailParams(ImageAsset\Thumbnail $thumbnail, Asset $asset): void
    {
        $thumbnailHelper = $this->getProject()
                                ->thumbnailHelper();
        $srcUrl = $thumbnail->getPath(false);
//        Pimcore >= 10.6
//        $srcUrl = $thumbnail->getPath(['deferredAllowed' => false, 'frontend' => true]);
        if ($thumbnailHelper->isNotSupportedImage($srcUrl)) {
            $srcUrl = $thumbnailHelper->replaceNotSupported($srcUrl);
            $fileSize = 259010;
        } else {
            if (!file_exists($thumbnail->getLocalFile())) {
                $this->notifyMissingAsset(
                    sprintf("Thumbnail '%s' could not be created.", $thumbnail->getLocalFile()),
                    $thumbnail->getAsset()
                              ->getId()
                );

                return;
            }
            $fileSize = $thumbnail->getFileSize();

            if ($this->getProject()
                     ->config()
                     ->offsetGet('file_storage_mtime')) {
                $this->setParam('mtime', @filemtime($thumbnail->getLocalFile()));
            } else {
                $this->setParam('mtime', (int)$asset->getModificationDate());
            }
        }

        $src = parse_url($srcUrl, PHP_URL_PATH);
        $srcUrl = $thumbnailHelper->prependHostUrl($srcUrl);

        $this->setParam('src', urldecode($src));
        $this->setParam('srcFileSize', $fileSize);
        $this->setParam('srcUrl', $srcUrl);
        $this->setParam('thumbnailUrl', $srcUrl);
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

    /**
     * Usage of setSrc not allowed in ImageBox. Use setAsset instead.
     *
     * @param string $src
     *
     * @return FileBox
     * @throws \Exception
     */
    public function setSrc(string $src): FileBox
    {
        throw new \Exception('Usage of setSrc not allowed in ImageBox. Use setAsset instead.');
    }

    /**
     * Returns allowed MIME-Types
     *
     * @return array
     * @throws \Exception
     */
    private function getAllowedMimeTypes(): array
    {
        if (!isset(self::$allowedMimeTypes)) {
            self::$allowedMimeTypes = self::$baseAllowedMimeTypes;
            $svgEnabled = $this->getProjectsManager()
                               ->getProject()
                               ->config()
                               ->offsetGet('svg_support');
            if ($svgEnabled) {
                self::$allowedMimeTypes[] = self::MIME_TYPE_SVG;
            }
        }

        return self::$allowedMimeTypes;
    }
}
