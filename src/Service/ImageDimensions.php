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

use Pimcore\Model\Asset\Image;

/**
 * Helper service to get dimensions of image assets.
 *
 * @package Mds\PimPrint\CoreBundle\Service
 */
class ImageDimensions
{
    /**
     * Returns an array with the following image sizes:
     * - dpi
     * - width
     * - height
     * - width_mm
     * - height_mm
     *
     * @param Image $asset
     * @param array $defaultDpi
     *
     * @return array
     * @throws \Exception
     */
    public function getSizes(Image $asset, array $defaultDpi = [300, 300]): array
    {
        $dimensions = $asset->getDimensions();
        if (null === $dimensions) {
            throw new \Exception(
                sprintf(
                    "Image size could not be loaded for asset %s (%s).",
                    $asset->getId(),
                    $asset->getFilename()
                )
            );
        }
        $dpis = $this->getDpi($asset, $defaultDpi);

        return [
            'dpi'       => $dpis,
            'width'     => $dimensions['width'],
            'height'    => $dimensions['height'],
            'width_mm'  => $dimensions['width'] / ($dpis[0] / 25.4),
            'height_mm' => $dimensions['height'] / ($dpis[1] / 25.4)
        ];
    }

    /**
     * Returns the resolution (dpi) of $asset.
     * If no resolution can be read $default resolution is supposed.
     *
     * @param Image $asset
     * @param array $default
     *
     * @return array
     * @throws \Exception
     */
    public function getDpi(Image $asset, array $default = [300, 300]): array
    {
        $filePath = $asset->getLocalFile();
        $imageType = @exif_imagetype($filePath);
        if (IMAGETYPE_TIFF_MM == $imageType || IMAGETYPE_TIFF_II == $imageType) {
            try {
                return $this->loadDpiWithImagick($filePath);
            } catch (\Exception) {
                return $default;
            }
        }
        if (IMAGETYPE_JPEG == $imageType) {
            try {
                return $this->loadDpiFromJpegFile($filePath);
            } catch (\Exception) {
                return $default;
            }
        }

        return $default;
    }

    /**
     * Reads image resolution with Imagick extension.
     *
     * @param string $filePath
     *
     * @return array
     * @throws \ImagickException
     * @throws \Exception
     */
    protected function loadDpiWithImagick(string $filePath): array
    {
        if (false === class_exists('Imagick')) {
            throw new \Exception('No Imagick extension installed.');
        }
        $xSize = $ySize = null;

        $resource = new \Imagick($filePath);
        $imageResolution = $resource->getImageResolution();
        if (false === empty($imageResolution['x'])) {
            $xSize = $imageResolution['x'];
        }
        if (false === empty($imageResolution['y'])) {
            $ySize = $imageResolution['y'];
        }
        if ($xSize && $ySize) {
            return [$xSize, $ySize];
        }

        throw new \Exception('No resolution found.');
    }

    /**
     * Reads image resolution from JPEG exif data.
     *
     * @param string $filePath
     *
     * @return array
     * @throws \Exception
     */
    protected function loadDpiFromJpegFile(string $filePath): array
    {
        $exif = exif_read_data($filePath, 'IFD0');
        if (false === $exif) {
            throw new \Exception('No resolution found.');
        }
        $xSize = $ySize = null;
        if (isset($exif['XResolution']) && isset($exif['YResolution'])) {
            $xSize = floatval(preg_replace('@^(\\d+)/(\\d+)$@', '$1/$2', $exif['XResolution']));
            $ySize = floatval(preg_replace('@^(\\d+)/(\\d+)$@', '$1/$2', $exif['YResolution']));
        }
        if (!$xSize && !$ySize && $filePointer = fopen($filePath, 'r')) {
            $string = fread($filePointer, 20);
            fclose($filePointer);
            $data = bin2hex(substr($string, 14, 4));
            $xSize = hexdec(substr($data, 0, 4));
            $ySize = hexdec(substr($data, 4, 4));
        }
        if ($xSize && $ySize) {
            return [$xSize, $ySize];
        }

        throw new \Exception('No resolution found.');
    }
}
