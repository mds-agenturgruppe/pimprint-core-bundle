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
use Pimcore\Model\User;
use Pimcore\Security\User\UserLoader;
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
    protected ImageDimensions $imageDimensions;

    /**
     * SpecialChars helper service.
     *
     * @var SpecialChars
     */
    protected SpecialChars $specialChars;

    /**
     * PluginParameters instance.
     *
     * @var PluginParameters
     */
    protected PluginParameters $pluginParams;

    /**
     * Pimcore RequestHelper.
     *
     * @var RequestHelper
     */
    protected RequestHelper $requestHelper;

    /**
     * ThumbnailHelper service.
     *
     * @var ThumbnailHelper
     */
    protected ThumbnailHelper $thumbnailHelper;

    /**
     * Project configuration.
     *
     * @var Config
     */
    protected Config $config;

    /**
     * UrlGenerator instance.
     *
     * @var UrlGeneratorInterface
     */
    protected UrlGeneratorInterface $urlGenerator;

    /**
     * LocaleService instance.
     *
     * @var LocaleServiceInterface
     */
    protected LocaleServiceInterface $localeService;

    /**
     * Pimcore UserLoader
     *
     * @var UserLoader
     */
    private UserLoader $userLoader;

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
     * Sets PluginParameters helper service.
     *
     * @param PluginParameters $pluginParameters
     *
     * @return void
     */
    public function setPluginParams(PluginParameters $pluginParameters): void
    {
        $this->pluginParams = $pluginParameters;
    }

    /**
     * Sets Pimcore RequestHelper service.
     *
     * @param RequestHelper $requestHelper
     *
     * @return void
     */
    public function setRequestHelper(RequestHelper $requestHelper): void
    {
        $this->requestHelper = $requestHelper;
    }

    /**
     * Returns current request.
     *
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->requestHelper->getMainRequest();
    }

    /**
     * Returns PluginParameters instance to access parameters from InDesign plugin.
     *
     * @return PluginParameters
     */
    public function pluginParams(): PluginParameters
    {
        return $this->pluginParams;
    }

    /**
     * Sets ImageDimensions helper service.
     *
     * @param ImageDimensions $imageDimensions
     *
     * @return void
     */
    public function setImageDimensions(ImageDimensions $imageDimensions): void
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
     *
     * @return void
     */
    public function setSpecialChars(SpecialChars $specialChars): void
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
     *
     * @return void
     */
    public function setThumbnailHelper(ThumbnailHelper $thumbnailHelper): void
    {
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
     *
     * @return void
     */
    final public function setConfig(Config $config): void
    {
        $this->config = $config;
    }

    /**
     * Sets urlGenerator.
     *
     * @param UrlGeneratorInterface $urlGenerator
     *
     * @return void
     */
    public function setUrlGenerator(UrlGeneratorInterface $urlGenerator): void
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Returns urlGenerator.
     *
     * @return UrlGeneratorInterface
     */
    protected function getUrlGenerator(): UrlGeneratorInterface
    {
        return $this->urlGenerator;
    }

    /**
     * Sets LocaleService instance.
     *
     * @param LocaleServiceInterface $localeService
     *
     * @return void
     */
    public function setLocaleService(LocaleServiceInterface $localeService): void
    {
        $this->localeService = $localeService;
    }

    /**
     * Sets Pimcore UserLoader
     *
     * @param UserLoader $userLoader
     *
     * @return void
     */
    public function setUserLoader(UserLoader $userLoader): void
    {
        $this->userLoader = $userLoader;
    }

    /**
     * Returns Pimcore UserLoader
     *
     * @return UserLoader
     */
    protected function getUserLoader(): UserLoader
    {
        return $this->userLoader;
    }

    /**
     * Returns currently logged in Pimcore User
     *
     * @return User
     */
    protected function getUser(): User
    {
        return $this->userLoader->getUser();
    }

    /**
     * Returns absolute host url.
     * Convenience method to have Request parameter added automatically.
     *
     * @return string
     * @throws \Exception
     */
    public function getHostUrl(): string
    {
        return $this->config()
                    ->getHostUrl($this->getRequest());
    }

    /**
     * Asserts that service is initialized correctly.
     * Integrated to show appropriate exception, when concrete project service hasn't right parent configuration.
     *
     * @return void
     * @throws \Exception
     */
    final public function assertServiceInitialized(): void
    {
        $services = [
            $this->requestHelper,
            $this->imageDimensions,
            $this->specialChars,
            $this->pluginParams,
            $this->thumbnailHelper,
            $this->config,
            $this->localeService,
            $this->userLoader,
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
