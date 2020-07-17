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

namespace Mds\PimPrint\CoreBundle\InDesign\Command\Traits;

use Mds\PimPrint\CoreBundle\InDesign\Command\AbstractCommand;
use Mds\PimPrint\CoreBundle\InDesign\Command\ImageBox;
use Mds\PimPrint\CoreBundle\InDesign\Command\ImageCollectorInterface;

/**
 * Trait ImageCollectorTrait
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command\Traits
 */
trait ImageCollectorTrait
{
    /**
     * Array with all images used in command.
     *
     * @var array
     */
    protected $collectedImages = [];

    /**
     * Returns collected images.
     *
     * @return array
     */
    public function getCollectedImages()
    {
        return $this->collectedImages;
    }

    /**
     * Collects image in $command if $command is ImageBox
     *
     * @param AbstractCommand $command
     */
    protected function collectImageCommand(AbstractCommand $command)
    {
        if ($command instanceof ImageBox) {
            $this->collectImage($command);
        }
    }

    /**
     * Registers asset in $imageBox in $images.
     *
     * @param ImageBox $imageBox
     */
    protected function collectImage(ImageBox $imageBox)
    {
        try {
            if (false === $this->getProject()
                               ->config()
                               ->isAssetDownloadEnabled()) {
                return;
            }
            $assetId = (int)$imageBox->getParam('assetId');
            if (empty($assetId)) {
                return;
            }
            $this->collectedImages[$assetId] = [
                'assetId'     => $assetId,
                'src'         => $imageBox->getParam('src'),
                'srcUrl'      => $imageBox->getParam('srcUrl'),
                'srcFileSize' => $imageBox->getParam('srcFileSize'),
                'mtime'       => $imageBox->getParam('mtime'),
            ];
        } catch (\Exception $e) {
            //do nothing
        }
    }

    /**
     * If $element is instance of ImageCollectorInterface collectedImages will be added.
     *
     * @param mixed|ImageCollectorInterface $element
     */
    protected function addCollectedImages($element)
    {
        if ($element instanceof ImageCollectorInterface) {
            $this->collectedImages += $element->getCollectedImages();
        }
    }
}
