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

namespace Mds\PimPrint\CoreBundle\InDesign\Traits;

use Mds\PimPrint\CoreBundle\Service\AccessorTraits\ProjectsManagerTrait;

/**
 * Trait MissingAssetNotifierTrait
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Traits
 */
trait MissingAssetNotifierTrait
{
    use ProjectsManagerTrait;

    /**
     * Adds $message as notification for missing asset for $assetId.
     * If config variable imageWarningsOnPage is true a onPage message will be generated.
     * Otherwise, a offPage message will be generated.
     *
     * @param string $message
     * @param int    $assetId
     *
     * @return void
     * @throws \Exception
     */
    protected function notifyMissingAsset(string $message, int $assetId): void
    {
        $project = $this->getProjectsManager()
                        ->getProject();
        $project->getCommandQueue()
                ->incrementMissingAssetCounter($assetId);
        $project->addPageMessage(
            $message,
            $project->config()
                    ->isAssetWarningOnPage()
        );
    }

    /**
     * Adds preMessage if notification for first missing asset is added.
     *
     * @return void
     * @throws \Exception
     */
    protected function addMissingAssetPreMessage(): void
    {
        try {
            $project = $this->getProjectsManager()
                            ->getProject();
        } catch (\Exception $e) {
            return;
        }
        $missingAssets = $project->getCommandQueue()
                                 ->getMissingAssets();
        if (0 == $missingAssets['elements']) {
            return;
        }
        $amountMissingAssets = count($missingAssets['assetIds']);
        $message = sprintf(
            '%s %s used in %s %s missing.',
            $amountMissingAssets,
            $amountMissingAssets == 1 ? 'asset' : 'assets',
            $missingAssets['elements'],
            $missingAssets['elements'] == 1 ? 'box' : 'boxes'
        );
        if (true === $project->config()
                             ->isAssetWarningOnPage()) {
            $message .= '<br>Messages are rendered directly on the page.';
        }
        $project->addPreMessage($message);
    }
}
