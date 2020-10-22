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

use Pimcore\Model\Asset;

/**
 * Trait TemplateTrait
 *
 * @package Mds\PimPrint\CoreBundle\Project\Traits
 */
trait TemplateTrait
{
    /**
     * Returns InDesign template filename or Pimcore Asset object.
     * By default the template filename defined in project configuration is used.
     *
     * Can be overwritten in concrete projects to use values from Pimcore data model like fields or properties.
     *
     * @return string|Asset
     * @throws \Exception
     */
    protected function getTemplate()
    {
        $config = $this->config()
                       ->offsetGet('template', array());
        if (false === isset($config['default'])) {
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
     */
    public function getTemplateFilePath(string $filename)
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
     * @throws \Exception
     */
    final protected function buildTemplateSettings()
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
     * @throws \Exception
     */
    final private function addTemplateDownloadData(array &$settings)
    {
        $settings['url'] = $this->buildTemplateUrl();

        $template = $this->getTemplate();
        if ($template instanceof Asset) {
            $settings['mtime'] = @filemtime($template->getFileSystemPath());
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
     * Otherwise the template file form the project bundle is downloaded via:
     * \Mds\PimPrint\CoreBundle\Controller\InDesignController::downloadTemplateAction
     *
     * @return string
     * @throws \Exception
     */
    private function buildTemplateUrl()
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
