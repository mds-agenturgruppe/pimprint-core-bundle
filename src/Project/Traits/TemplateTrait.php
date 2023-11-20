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

namespace Mds\PimPrint\CoreBundle\Project\Traits;

use League\Flysystem\FilesystemException;
use Pimcore\Model\Asset;
use Pimcore\Tool\Storage;

/**
 * Trait TemplateTrait
 *
 * @package Mds\PimPrint\CoreBundle\Project\Traits
 */
trait TemplateTrait
{
    /**
     * Returns InDesign template filename or Pimcore Asset object.
     * By default, the template filename defined in project configuration is used.
     *
     * Can be overwritten in concrete projects to use values from Pimcore data model like fields or properties.
     *
     * @return string|Asset
     * @throws \Exception
     */
    protected function getTemplate(): Asset|string
    {
        $config = $this->config()
                       ->offsetGet('template');
        if (false === is_array($config) || false === isset($config['default'])) {
            throw new \Exception(
                sprintf(
                    "No default template defined for project '%s' in configuration.",
                    $this->getIdent()
                )
            );
        }

        return $config['default'];
    }

    /**
     * Returns file path of template $filename for current project.
     *
     * @param string $filename
     *
     * @return string
     * @throws \Exception
     */
    public function getTemplateFilePath(string $filename): string
    {
        return implode(
            [
                $this->config()
                     ->offsetGet('bundlePath'),
                $this->config()
                     ->offsetGet('template')['relative_path'],
                urldecode($filename)
            ]
        );
    }

    /**
     * Builds settings array for InDesign template file.
     *
     * @return array
     * @throws FilesystemException
     * @throws \Exception
     */
    final protected function buildTemplateSettings(): array
    {
        $settings = [
            'download' => $this->config()
                               ->offsetGet('template')['download'],
        ];
        if (false === $settings['download'] || false === $this->isGenerationActive()) {
            return $settings;
        }
        $this->addTemplateDownloadData($settings);

        return $settings;
    }

    /**
     * Adds template download data to $settings array.
     *
     * @param array $settings
     *
     * @return void
     * @throws \Exception
     * @throws FilesystemException
     */
    private function addTemplateDownloadData(array &$settings): void
    {
        $settings['url'] = $this->buildTemplateUrl();

        $template = $this->getTemplate();
        if ($template instanceof Asset) {
            $storage = Storage::get('asset');
            if (false === $storage->fileExists($template->getFullPath())) {
                throw new \Exception(
                    sprintf('InDesign template file not found: %s', $template->getFullPath())
                );
            }
            $settings['mtime'] = $storage->lastModified($template->getFullPath());
            $settings['fileSize'] = $template->getFileSize();

            return;
        }

        $filePath = $this->getTemplateFilePath($template);
        if (false === file_exists($filePath)) {
            throw new \Exception(sprintf('InDesign template file not found on server: %s', $template));
        }
        $settings['mtime'] = @filemtime($filePath);
        $settings['fileSize'] = @filesize($filePath);
    }

    /**
     * Builds the template download url.
     * If template is a Pimcore Asset object the public url is used.
     * Otherwise, the template file form the project bundle is downloaded via:
     * \Mds\PimPrint\CoreBundle\Controller\InDesignController::downloadTemplateAction
     *
     * @return string
     * @throws \Exception
     */
    private function buildTemplateUrl(): string
    {
        $template = $this->getTemplate();
        if ($template instanceof Asset) {
            return $this->getHostUrl() . urlencode_ignore_slash($template->getRealFullPath());
        }

        return $this->getHostUrl() . $this->getUrlGenerator()
                                          ->generate(
                                              'mds_pimprint_downlaod_template',
                                              [
                                                  'identifier'   => $this->getIdent(),
                                                  'templateFile' => urlencode($template),
                                              ]
                                          );
    }
}
