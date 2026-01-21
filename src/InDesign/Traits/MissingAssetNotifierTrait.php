<?php
/**
 * mds. Agenturgruppe GmbH
 *
 * This source file is available under the terms of the
 * mds. Commercial License (MCL)
 *
 * Full copyright and license information is available in
 * LICENSE.md, which is distributed with this source code.
 *
 * @copyright Copyright (c) mds. Agenturgruppe GmbH (https://www.mds.eu)
 * @license   mds. Commercial License (MCL)
 */

namespace Mds\PimPrint\CoreBundle\InDesign\Traits;

use Mds\PimPrint\CoreBundle\Service\AccessorTraits\ProjectsManagerTrait;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

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
     * If config variable imageWarningsOnPage is true, an onPage message will be generated.
     * Otherwise, an offPage message will be generated.
     *
     * @param string $message
     * @param int    $assetId
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Exception
     */
    protected function notifyMissingAsset(string $message, int $assetId): void
    {
        $project = $this->getProjectsManager()
                        ->getProject();
        $project->commandQueue()
                ->incrementMissingAssetCounter($assetId);
        $project->addPageMessage(
            $message,
            $project->config()
                    ->isAssetWarningOnPage()
        );
    }

    /**
     * Adds preMessage if notification for the first missing asset is added.
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Exception
     */
    protected function addMissingAssetPreMessage(): void
    {
        try {
            $project = $this->getProjectsManager()
                            ->getProject();
        } catch (\Exception) {
            return;
        }
        $missingAssets = $project->commandQueue()
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
        if (
            $project->config()
                    ->isAssetWarningOnPage()
        ) {
            $message .= '<br>Messages are rendered directly on the page.';
        }
        $project->addPreMessage($message);
    }
}
