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
    protected array $collectedImages = [];

    /**
     * Returns collected images.
     *
     * @return array
     */
    public function getCollectedImages(): array
    {
        return $this->collectedImages;
    }

    /**
     * Collects image in $command if $command is ImageBox
     *
     * @param AbstractCommand $command
     *
     * @return void
     */
    protected function collectImageCommand(AbstractCommand $command): void
    {
        if ($command instanceof ImageBox) {
            $this->collectImage($command);
        }
    }

    /**
     * Registers asset in $imageBox in $images.
     *
     * @param ImageBox $imageBox
     *
     * @return void
     */
    protected function collectImage(ImageBox $imageBox): void
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
        } catch (\Exception) {
            //do nothing
        }
    }

    /**
     * If $element is instance of ImageCollectorInterface collectedImages will be added.
     *
     * @param mixed $element
     *
     * @return void
     */
    protected function addCollectedImages(mixed $element): void
    {
        if ($element instanceof ImageCollectorInterface) {
            $this->collectedImages += $element->getCollectedImages();
        }
    }
}
