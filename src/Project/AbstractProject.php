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

namespace Mds\PimPrint\CoreBundle\Project;

use League\Flysystem\FilesystemException;
use Mds\PimPrint\CoreBundle\InDesign\Command\AbstractCommand;
use Mds\PimPrint\CoreBundle\InDesign\Command\GoToPage;
use Mds\PimPrint\CoreBundle\InDesign\Command\OpenDocument;
use Mds\PimPrint\CoreBundle\InDesign\Command\RemoveEmptyLayers;
use Mds\PimPrint\CoreBundle\InDesign\Command\RemoveEmptyPages;
use Mds\PimPrint\CoreBundle\InDesign\Command\Variable;
use Mds\PimPrint\CoreBundle\Project\Interfaces\RenderingProjectInterface;
use Mds\PimPrint\CoreBundle\Project\Traits\BoxIdentTrait;
use Mds\PimPrint\CoreBundle\Project\Traits\FormFieldsTrait;
use Mds\PimPrint\CoreBundle\Project\Traits\InDesignTemplateTrait;
use Mds\PimPrint\CoreBundle\Service\CommandQueue;
use Mds\PimPrint\CoreBundle\Service\ImageDimensions;
use Mds\PimPrint\CoreBundle\Service\PluginParameters;
use Mds\PimPrint\CoreBundle\Service\SpecialChars;
use Mds\PimPrint\CoreBundle\Service\ThumbnailHelper;
use Pimcore\Http\RequestHelper;
use Pimcore\Localization\IntlFormatter;
use Pimcore\Localization\LocaleServiceInterface;
use Pimcore\Model\Asset;
use Pimcore\Model\User;
use Pimcore\Security\User\UserLoader;
use Pimcore\Tool;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Service\ServiceMethodsSubscriberTrait;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

/**
 * Class AbstractProject
 *
 * @SuppressWarnings("PHPMD.CouplingBetweenObjects")
 *
 * @package Mds\PimPrint\CoreBundle\Project
 */
abstract class AbstractProject implements ServiceSubscriberInterface, RenderingProjectInterface
{
    use ServiceMethodsSubscriberTrait;
    use InDesignTemplateTrait;
    use FormFieldsTrait;
    use BoxIdentTrait;

    /**
     * Project configuration.
     *
     * @var Config
     */
    protected Config $config;

    /**
     * Indicated if a generation of the project is active.
     *
     * @var bool
     */
    private bool $generationActive = false;

    /**
     * Array with messages displayed in InDesign Plugin before rendering.
     *
     * @var array
     */
    protected array $preMessages = [];

    /**
     * {@inheritDoc}
     *
     * @return array
     */
    public static function getSubscribedServices(): array
    {
        return [
            CommandQueue::class           => CommandQueue::class,
            RequestHelper::class          => RequestHelper::class,
            PluginParameters::class       => PluginParameters::class,
            ImageDimensions::class        => ImageDimensions::class,
            SpecialChars::class           => SpecialChars::class,
            ThumbnailHelper::class        => ThumbnailHelper::class,
            UrlGeneratorInterface::class  => UrlGeneratorInterface::class,
            LocaleServiceInterface::class => LocaleServiceInterface::class,
            IntlFormatter::class          => IntlFormatter::class,
            UserLoader::class             => UserLoader::class,
        ];
    }

    /**
     * Returns all publications in the tree structure to display in InDesign-Plugin.
     * Extend in concrete rendering Project if default plugin_element publications are active.
     *
     * @return array
     */
    public function getPublicationsTree(): array
    {
        return [];
    }

    /**
     * Generates PimPrint commands to build a publication in InDesign.
     *
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Exception
     */
    final public function run(): array
    {
        $this->generationActive = true;
        $this->buildPublication();

        return $this->commandQueue()
                    ->getCommands();
    }

    /**
     * Convenience method that initializes renderMode, opens InDesign template and jumps to first page.
     *
     * @param bool $openFirstPage
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Exception
     */
    protected function startRendering(bool $openFirstPage = true): void
    {
        $this->initFrontend();
        $this->initRenderMode()
             ->initInDesignDocument();
        if ($openFirstPage) {
            $this->addCommand(new GoToPage(1, false));
        }
    }

    /**
     * Convenience method that is called at the end of the rendering process.
     *
     * @param bool $removeEmptyLayers
     * @param bool $removeEmptyPages
     *
     * @return AbstractProject
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function stopRendering(bool $removeEmptyLayers = true, bool $removeEmptyPages = true): AbstractProject
    {
        if ($removeEmptyLayers) {
            $this->addCommand(new RemoveEmptyLayers());
        }
        if ($removeEmptyPages) {
            $this->addCommand(new RemoveEmptyPages());
        }

        return $this;
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
     * Returns project configuration
     *
     * @return Config
     */
    public function config(): Config
    {
        return $this->config;
    }

    /**
     * Template method for post-processing available languages sent to InDesign plugin.
     *
     * @param array $languages
     *
     * @return void
     */
    protected function postProcessLanguages(array &$languages): void
    {
    }

    /**
     * Returns the configured project name.
     *
     * Extend if the name should be dynamic.
     *
     * @return string
     * @throws \Exception
     */
    public function getName(): string
    {
        return $this->config->offsetGet('name', 'Undefined');
    }

    /**
     * Adds $message to display in InDesign Plugin before rendering.
     *
     * @param string $message
     *
     * @return AbstractProject
     */
    public function addPreMessage(string $message): AbstractProject
    {
        $this->preMessages[] = $message;

        return $this;
    }

    /**
     * Returns pre rendering messages.
     *
     * @return array
     */
    public function getPreMessages(): array
    {
        return $this->preMessages;
    }

    /**
     * Convenience (facade) method to add a PageMessage command to CommandQueue.
     *
     * @param string $message
     * @param bool   $onPage
     *
     * @return AbstractProject
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Exception
     */
    public function addPageMessage(string $message, bool $onPage = false): AbstractProject
    {
        $this->commandQueue()
             ->addPageMessage($message, $onPage);

        return $this;
    }

    /**
     * Convenience method to accessing 'ident' config.
     *
     * @return string
     * @throws \Exception
     */
    public function getIdent(): string
    {
        return $this->config()
                    ->offsetGet('ident', 'Undefined');
    }

    /**
     * Returns project info array.
     *
     * @return array
     * @throws \Exception
     */
    final public function getInfo(): array
    {
        return [
            'name'       => $this->getName(),
            'identifier' => $this->getIdent()
        ];
    }

    /**
     * Returns languages to be displayed in InDesign-Plugin.
     *
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getLanguages(): array
    {
        $languages = [];
        $locale = $this->getUser()
                       ->getLanguage();

        if (!Tool::isValidLanguage($locale)) {
            $locale = Tool::getDefaultLanguage();
        }

        foreach ($this->getUserLanguages() as $code) {
            $label = \Locale::getDisplayLanguage($code, $locale);
            $displayRegion = \Locale::getDisplayRegion($code, $locale);

            if ($displayRegion) {
                $label .= ' (' . $displayRegion . ')';
            }

            if ($label) {
                $label .= ' (' . $code . ')';
            } else {
                $label = $code;
            }

            $languages[$label] = [
                'iso'   => $code,
                'label' => $label,
            ];
        }

        ksort($languages);
        $this->postProcessLanguages($languages);

        return array_values($languages);
    }

    /**
     * Builds project settings for InDesign plugin.
     *
     * @return array
     * @throws FilesystemException
     * @throws \Exception
     */
    public function getSettings(): array
    {
        return [
            'assets'             => [
                'download'    => $this->config()
                                      ->offsetGet('assets')['download'],
                'preDownload' => $this->config()
                                      ->offsetGet('assets')['pre_download']
            ],
            'template'           => $this->buildTemplateSettings(),
            'createUpdateLayers' => $this->config()
                                         ->offsetGet('create_update_layers'),
        ];
    }

    /**
     * Returns languages for the current user.
     * For admin user all activated languages are returned.
     * Otherwise, all assigned content languages are returned.
     *
     * Template method can be overwritten in concrete projects to have e.g. workspace languages used.
     *
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getUserLanguages(): array
    {
        $user = $this->getUser();

        return $user->isAdmin() ? Tool::getValidLanguages() : $user->getContentLanguages();
    }

    /**
     * Convenience method to access current rendered language.
     *
     * @return string
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Exception
     */
    public function getLanguage(): string
    {
        return $this->pluginParams()
                    ->get(PluginParameters::PARAM_LANGUAGE);
    }

    /**
     * Returns CommandQueue used by project.
     *
     * @return CommandQueue
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function commandQueue(): CommandQueue
    {
        return $this->container->get(CommandQueue::class);
    }

    /**
     * Legacy method to access CommandQueue.
     *
     * @return CommandQueue
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @deprecated use commandQueue() instead
     */
    public function getCommandQueue(): CommandQueue
    {
        return $this->commandQueue();
    }

    /**
     * Convenience (facade) method to add $command to CommandQueue.
     *
     * @param AbstractCommand $command
     *
     * @return AbstractProject
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Exception
     */
    protected function addCommand(AbstractCommand $command): AbstractProject
    {
        $this->commandQueue()
             ->addCommand($command);

        return $this;
    }

    /**
     * Convenience (facade) method to add $commands array to CommandQueue.
     *
     * @param array $commands
     *
     * @return AbstractProject
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Exception
     */
    protected function addCommands(array $commands): AbstractProject
    {
        $this->commandQueue()
             ->addCommands($commands);

        return $this;
    }

    /**
     * Returns absolute host url.
     * Convenience method to have Request parameter added automatically.
     *
     * @return string
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Exception
     */
    public function getHostUrl(): string
    {
        return $this->config()
                    ->getHostUrl($this->getRequest());
    }

    /**
     * Returns true if the current request generated a project.
     *
     * @return bool
     */
    final public function isGenerationActive(): bool
    {
        return $this->generationActive;
    }

    /**
     * Initializes Pimcore frontend for rendered publication.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function initFrontend(): void
    {
        $this->setPimcoreLocales();
    }

    /**
     * Sets current rendered language as locale in Request and Pimcore services.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Exception
     */
    protected function setPimcoreLocales(): void
    {
        $locale = $this->getLanguage();
        if (!Tool::isValidLanguage($locale)) {
            throw new \Exception("Language '$locale' is no valid Pimcore language.");
        }
        $this->requestHelper()
             ->getRequest()
             ->setLocale($locale);

        $this->localeService()
             ->setLocale($locale);

        $this->intlFormatter()
             ->setLocale($locale);
    }

    /**
     * Sets PHP settings for generation mode.
     *
     * @return AbstractProject
     * @throws \Exception
     */
    protected function initRenderMode(): AbstractProject
    {
        $this->setPhpSettings();
        $this->setNumericLocale();

        return $this;
    }

    /**
     * Sets PHP settings.
     *
     * @throws \Exception
     */
    protected function setPhpSettings(): void
    {
        set_time_limit(
            $this->config()
                 ->offsetGet('php_time_limit')
        );
        ini_set(
            'memory_limit',
            $this->config()
                 ->offsetGet('php_memory_limit')
        );
    }

    /**
     * Sets locale for LC_NUMERIC according to PimPrint configuration.
     *
     * @throws \Exception
     */
    protected function setNumericLocale(): void
    {
        $locales = $this->config()
                        ->offsetGet('lc_numeric');
        if (empty($locales)) {
            return;
        }
        setlocale(LC_NUMERIC, $locales);
    }

    /**
     * Opens a new InDesign document and loads the InDesign template parameter template file.
     *
     * @return AbstractProject
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Exception
     */
    final protected function initInDesignDocument(): AbstractProject
    {
        $template = $this->getTemplate();
        if ($template instanceof Asset) {
            $template = $template->getFilename();
        }
        //Declare the current open InDesign document as the target document to generate publication in.
        $this->addCommand(new OpenDocument(OpenDocument::TYPE_USECURRENT, $this->getLanguage()))
            //opens the InDesign template document.
             ->addCommand(new OpenDocument(OpenDocument::TYPE_TEMPLATE, '0', $template))
             ->addCommand(new Variable('GENERATED_AT', time()));

        return $this;
    }

    /**
     * Returns RequestHelper
     *
     * @return RequestHelper
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function requestHelper(): RequestHelper
    {
        return $this->container->get(RequestHelper::class);
    }

    /**
     * Returns current request.
     *
     * @return Request
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getRequest(): Request
    {
        return $this->requestHelper()
                    ->getMainRequest();
    }

    /**
     * Returns PluginParameters
     *
     * @return PluginParameters
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function pluginParams(): PluginParameters
    {
        return $this->container->get(PluginParameters::class);
    }

    /**
     * Returns ImageDimensions
     *
     * @return ImageDimensions
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function imageDimensions(): ImageDimensions
    {
        return $this->container->get(ImageDimensions::class);
    }

    /**
     * Returns SpecialChars
     *
     * @return SpecialChars
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function specialChars(): SpecialChars
    {
        return $this->container->get(SpecialChars::class);
    }

    /**
     * Returns ThumbnailHelper
     *
     * @return ThumbnailHelper
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function thumbnailHelper(): ThumbnailHelper
    {
        $helper = $this->container->get(ThumbnailHelper::class);
        if (!$helper instanceof ThumbnailHelper) {
            throw new \RuntimeException('ThumbnailHelper must be an instance of ' . ThumbnailHelper::class);
        }
        $helper->setProject($this);
        $helper->validateAssetThumbnail();

        return $helper;
    }

    /**
     * Returns Pimcore LocaleService
     *
     * @return LocaleServiceInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function localeService(): LocaleServiceInterface
    {
        return $this->container->get(LocaleServiceInterface::class);
    }

    /**
     * Returns Pimcore IntlFormatter
     *
     * @return IntlFormatter
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function intlFormatter(): IntlFormatter
    {
        return $this->container->get(IntlFormatter::class);
    }

    /**
     * Returns Pimcore UrlGenerator
     *
     * @return UrlGeneratorInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function urlGenerator(): UrlGeneratorInterface
    {
        return $this->container->get(UrlGeneratorInterface::class);
    }

    /**
     * Returns Pimcore UserLoader
     * @return UserLoader
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function userLoader(): UserLoader
    {
        return $this->container->get(UserLoader::class);
    }

    /**
     * Returns currently logged in Pimcore User
     *
     * @return User
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getUser(): User
    {
        return $this->userLoader()
                    ->getUser();
    }
}
