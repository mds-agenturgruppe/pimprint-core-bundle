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

namespace Mds\PimPrint\CoreBundle\Service;

use Mds\PimPrint\CoreBundle\Project\AbstractProject;
use Mds\PimPrint\CoreBundle\Project\Config;
use Pimcore\Http\RequestHelper;
use Pimcore\Model\Asset\Image\Thumbnail\Config as ThumbnailConfig;

/**
 * Class ThumbnailHelper
 *
 * @package Mds\PimPrint\CoreBundle\Service
 */
class ThumbnailHelper
{
    /**
     * Thumbnail name for PimPrint default preview image.
     *
     * @var string
     */
    const THUMBNAIL_NAME = 'mds_pimprint_preview';

    /**
     * File used for 'filetype-not-supported' image.
     *
     * @var string
     */
    const IMAGE_NOT_SUPPORTED = '/bundles/mdspimprintcore/img/filetype-not-supported.eps';

    /**
     * Allowed thumbnail formats to have InDesign compatible image thumbnails.
     *
     * @var array
     */
    protected array $allowedThumbnailFormats = [
        'SOURCE',
        'PNG',
        'GIF',
        'JPEG',
        'PJPEG',
        'TIFF',
    ];

    /**
     * AbstractProject instance.
     *
     * @var AbstractProject
     */
    protected AbstractProject $project;

    /**
     * ThumbnailHelper constructor.
     *
     * @param RequestHelper $requestHelper
     */
    public function __construct(RequestHelper $requestHelper)
    {
        $this->disableWebpSupport($requestHelper);
    }

    /**
     * Sets $project.
     *
     * @param AbstractProject $project
     *
     * @return void
     */
    final public function setProject(AbstractProject $project): void
    {
        $this->project = $project;
    }

    /**
     * Validates configured asset thumbnail configuration exists and if it generated images usable in InDesign.
     *
     * @return void
     * @throws \Exception
     */
    public function validateAssetThumbnail(): void
    {
        $config = $this->getProjectConfig()
                       ->offsetGet('assets');
        if (false === isset($config['thumbnail'])) {
            return;
        }
        $config = $config['thumbnail'];
        $thumbnail = ThumbnailConfig::getByName($config);
        if (!$thumbnail instanceof ThumbnailConfig) {
            throw new \Exception(
                sprintf(
                    "Thumbnail config '%s' does not exist for project '%s'.",
                    $config,
                    $this->project->getIdent()
                )
            );
        }
        if (false === in_array($thumbnail->getFormat(), $this->allowedThumbnailFormats)) {
            throw new \Exception(
                sprintf(
                    "Thumbnail config '%s' for project '%s' uses no InDesign compatible file format '%s'.",
                    $config,
                    $this->project->getIdent(),
                    $thumbnail->getFormat()
                )
            );
        }
    }

    /**
     * Returns configured project or PimPrint thumbnail config.
     * As InDesign can't handle SVG assets, SVGs are always rasterized.
     *
     * @param string|null $thumbnailName
     *
     * @return ThumbnailConfig
     * @throws \Exception
     */
    public function getThumbnailConfig(string $thumbnailName = null): ThumbnailConfig
    {
        $thumbnailConfig = null;
        if (null === $thumbnailName) {
            $config = $this->getProjectConfig()
                           ->offsetGet('assets');
            if (true === isset($config['thumbnail'])) {
                $thumbnailName = $config['thumbnail'];
            } else {
                $thumbnailConfig = $this->createDefaultThumbnailConfig();
            }
        }
        if (null !== $thumbnailName) {
            $thumbnailConfig = ThumbnailConfig::getByName($thumbnailName);
            if (false === $thumbnailConfig instanceof ThumbnailConfig) {
                throw new \Exception(sprintf("Thumbnail config '%s' not found.", $thumbnailName));
            }
        }
        if (false === $thumbnailConfig instanceof ThumbnailConfig) {
            throw new \Exception('No thumbnail config for generating preview image');
        }
        $thumbnailConfig->setRasterizeSVG(true);

        return $thumbnailConfig;
    }

    /**
     * Creates default PimPrint thumbnail config.
     *
     * @return ThumbnailConfig
     * @throws \Exception
     */
    protected function createDefaultThumbnailConfig(): ThumbnailConfig
    {
        $config = new ThumbnailConfig();
        $config->setName(self::THUMBNAIL_NAME);
        $config->addItem('scaleByWidth', ['width' => 300]);
        $config->addItem(
            'setBackgroundImage',
            [
                'path' => '/bundles/pimcoreadmin/img/tree-preview-transparent-background.png',
                'mode' => 'asTexture'
            ]
        );
        $config->setQuality(60);
        $config->setFormat('JPEG');

        return $config;
    }

    /**
     * Returns Config of current project.
     *
     * @return Config
     */
    protected function getProjectConfig(): Config
    {
        return $this->project->config();
    }

    /**
     * InDesign can't handle webp support. We disable it for thumbnails.
     *
     * @param RequestHelper $requestHelper
     *
     * @return void
     */
    private function disableWebpSupport(RequestHelper $requestHelper): void
    {
        if (false === $requestHelper->hasMainRequest()) {
            return;
        }
        //not nice but this way we disable the "InDesign browser" detection
        //@see \Pimcore\Tool\Frontend::determineClientWebpSupport
        // not nice to do a browser detection but for now the easiest way to get around the topic described in #4345
        $requestHelper->getMainRequest()->headers->set('User-Agent', '');

        $accept = $requestHelper->getMainRequest()->headers->get('Accept');
        $requestHelper->getMainRequest()->headers->set(
            'Accept',
            str_replace(['image/webp,', 'image/webp;'], '', $accept)
        );
    }

    /**
     * Returns true if $path contains 'filetype-not-supported.svg'.
     *
     * @param string $path
     *
     * @return bool
     */
    public function isNotSupportedImage(string $path): bool
    {
        return str_contains($path, 'filetype-not-supported.svg');
    }

    /**
     * As InDesign can't use SVG images we use 'filetype-not-supported.eps' error image.
     *
     * @param string $path
     *
     * @return string
     */
    public function replaceNotSupported(string $path): string
    {
        if (false === $this->isNotSupportedImage($path)) {
            return $path;
        }

        return self::IMAGE_NOT_SUPPORTED;
    }
}
