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

use Mds\PimPrint\CoreBundle\Project\AbstractProject;
use Mds\PimPrint\CoreBundle\Project\Config;
use Mds\PimPrint\CoreBundle\Service\ImageDimensions;
use Mds\PimPrint\CoreBundle\Service\PluginParameters;
use Mds\PimPrint\CoreBundle\Service\SpecialChars;
use Mds\PimPrint\CoreBundle\Service\ThumbnailHelper;
use Pimcore\Http\RequestHelper;
use Pimcore\Localization\LocaleServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Trait ServicesTrait
 *
 * @package Mds\PimPrint\CoreBundle\Project\Traits
 */
trait ServicesTrait
{
    /**
     * ImageDimensions helper service.
     *
     * @var ImageDimensions
     */
    protected $imageDimensions;

    /**
     * SpecialChars helper service.
     *
     * @var SpecialChars
     */
    protected $specialChars;

    /**
     * PluginParameters instance.
     *
     * @var PluginParameters
     */
    protected $pluginParams;

    /**
     * Pimcore RequestHelper.
     *
     * @var RequestHelper
     */
    protected $requestHelper;

    /**
     * ThumbnailHelper service.
     *
     * @var ThumbnailHelper
     */
    protected $thumbnailHelper;

    /**
     * Project configuration.
     *
     * @var Config
     */
    protected $config;

    /**
     * UrlGenerator instance.
     *
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * LocaleService instance.
     *
     * @var LocaleServiceInterface
     */
    protected $localeService;

    /**
     * Sets PluginParameters helper service.
     *
     * @param PluginParameters $pluginParameters
     */
    public function setPluginParams(PluginParameters $pluginParameters)
    {
        $this->pluginParams = $pluginParameters;
    }

    /**
     * Sets Pimcore RequestHelper service.
     */
    public function setRequestHelper(RequestHelper $requestHelper)
    {
        $this->requestHelper = $requestHelper;
    }

    /**
     * Returns current request.
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->requestHelper->getRequest();
    }

    /**
     * Returns PluginParameters instance to access parameters from InDesign plugin.
     *
     * @return PluginParameters
     */
    public function pluginParams()
    {
        return $this->pluginParams;
    }

    /**
     * Sets ImageDimensions helper service.
     *
     * @param ImageDimensions $imageDimensions
     */
    public function setImageDimensions(ImageDimensions $imageDimensions)
    {
        $this->imageDimensions = $imageDimensions;
    }

    /**
     * Returns ImageDimensions helper service.
     *
     * @return ImageDimensions
     */
    public function imageDimensions(): ImageDimensions
    {
        return $this->imageDimensions;
    }

    /**
     * Sets SpecialChars helper service.
     *
     * @param SpecialChars $specialChars
     */
    public function setSpecialChars(SpecialChars $specialChars)
    {
        $this->specialChars = $specialChars;
    }

    /**
     * Returns SpecialChars helper service.
     *
     * @return SpecialChars
     */
    public function specialChars(): SpecialChars
    {
        return $this->specialChars;
    }

    /**
     * Sets ThumbnailHelper service.
     *
     * @param ThumbnailHelper $thumbnailHelper
     */
    public function setThumbnailHelper(ThumbnailHelper $thumbnailHelper)
    {
        /* @var AbstractProject $this */
        $thumbnailHelper->setProject($this);
        $this->thumbnailHelper = $thumbnailHelper;
    }

    /**
     * Returns ThumbnailHelper service.
     *
     * @return ThumbnailHelper
     */
    public function thumbnailHelper(): ThumbnailHelper
    {
        return $this->thumbnailHelper;
    }

    /**
     * Sets $config project configuration.
     *
     * @param Config $config
     */
    final public function setConfig(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Sets urlGenerator.
     *
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function setUrlGenerator(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Returns urlGenerator.
     *
     * @return UrlGeneratorInterface
     */
    protected function getUrlGenerator()
    {
        return $this->urlGenerator;
    }

    /**
     * Sets LocaleService instance.
     *
     * @param LocaleServiceInterface $localeService
     */
    public function setLocaleService(LocaleServiceInterface $localeService)
    {
        $this->localeService = $localeService;
    }

    /**
     * Returns project configuration
     *
     * @return Config
     */
    public function config(): Config
    {
        return $this->config;
    }

    /**
     * Returns absolute host url.
     * Convenience method to have Request parameter added automatically.
     *
     * @return string
     * @throws \Exception
     */
    public function getHostUrl()
    {
        return $this->config()
                    ->getHostUrl($this->getRequest());
    }

    /**
     * Asserts that service service is initialized correctly.
     * Integrated to show appropriate exception, when concrete project service hasn't right parent configuration.
     *
     * @throws \Exception
     */
    final public function assertServiceInitialized()
    {
        $services = [
            $this->requestHelper,
            $this->imageDimensions,
            $this->specialChars,
            $this->pluginParams,
            $this->thumbnailHelper,
            $this->config,
            $this->localeService,
        ];
        foreach ($services as $service) {
            if (null === $service) {
                throw new \Exception(
                    sprintf(
                        "PimPrint project service '%s' not defined correctly. Service must use parent '%s'.",
                        $this->config()
                             ->offsetGet('service'),
                        AbstractProject::class
                    )
                );
            }
        }

        $this->thumbnailHelper->validateAssetThumbnail();
    }
}
