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

namespace Mds\PimPrint\CoreBundle\Project;

use League\Flysystem\FilesystemException;
use Mds\PimPrint\CoreBundle\InDesign\Command\AbstractCommand;
use Mds\PimPrint\CoreBundle\InDesign\CommandQueue;
use Mds\PimPrint\CoreBundle\Project\Traits\BoxIdentTrait;
use Mds\PimPrint\CoreBundle\Project\Traits\FormFieldsTrait;
use Mds\PimPrint\CoreBundle\Project\Traits\ServicesTrait;
use Mds\PimPrint\CoreBundle\Project\Traits\RenderingTrait;
use Mds\PimPrint\CoreBundle\Project\Traits\TemplateTrait;
use Mds\PimPrint\CoreBundle\Service\PluginParameters;
use Pimcore\Model\User;
use Pimcore\Tool;

/**
 * AbstractProject all concrete rendering project services must extend.
 *
 * Class is registered as abstract service in 'src/Resources/config/services.yml'
 * and aliased as 'mds.pimprint.core.abstract_project' to be used in concrete service definitions as parent.
 *
 * Example:
 * {code}
 * Mds\PimPrint\DemoBundle\Service\CommandDemo:
 *   parent: mds.pimprint.core.abstract_project
 * {code}
 *
 * @package Mds\PimPrint\CoreBundle\Project
 */
abstract class AbstractProject
{
    use ServicesTrait;
    use TemplateTrait;
    use RenderingTrait;
    use FormFieldsTrait;
    use BoxIdentTrait;

    /**
     * CommandQueue instance.
     *
     * @var CommandQueue|null
     */
    private ?CommandQueue $commandQueue = null;

    /**
     * Array with messages displayed in InDesign Plugin before rendering.
     *
     * @var array
     */
    protected array $preMessages = [];

    /**
     * Generates InDesign Commands to build the selected publication in InDesign.
     *
     * @return void
     */
    abstract public function buildPublication(): void;

    /**
     * Returns all publications in tree structure to display in InDesign-Plugin.
     * Extend in concrete rendering Project if default plugin_element publications is active.
     *
     * @return array
     */
    public function getPublicationsTree(): array
    {
        return [];
    }

    /**
     * Convenience method to accessing 'name' config.
     *
     * @return string
     * @throws \Exception
     */
    public function getName(): string
    {
        return $this->config->offsetGet('name', 'Undefined');
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
     */
    final public function getLanguages(): array
    {
        $languages = [];
        $locale = $this->localeService->findLocale();

        if (null === $locale) {
            throw new \RuntimeException('Locale could not be found!');
        }

        if (!Tool::isValidLanguage($locale)) {
            $locale = Tool::getDefaultLanguage();
        }

        foreach ($this->getUserLanguages() as $code) {
            $languages[] = [
                'iso'   => $code,
                'label' => \Locale::getDisplayLanguage($code, $locale),
            ];
        }

        return $languages;
    }

    /**
     * Builds project settings for InDesign plugin.
     *
     * @return array
     * @throws FilesystemException
     * @throws \Exception
     */
    final public function getSettings(): array
    {
        return [
            'createUpdateInfoBox' => $this->config()
                                          ->offsetGet('create_update_info'),
            'assets'              => [
                'download'    => $this->config()
                                      ->offsetGet('assets')['download'],
                'preDownload' => $this->config()
                                      ->offsetGet('assets')['pre_download']
            ],
            'template'            => $this->buildTemplateSettings()
        ];
    }

    /**
     * Returns languages for current user.
     * For admin user all activated languages are returned.
     * Otherwise, all assigned content languages are returned.
     *
     * Template method can be overwritten in concrete projects to have e.g. workspace languages used.
     *
     * @return array
     */
    protected function getUserLanguages(): array
    {
        $user = Tool\Admin::getCurrentUser();

        if (!$user instanceof User) {
            return Tool::getValidLanguages();
        }

        return true === $user->isAdmin() ? Tool::getValidLanguages() : $user->getContentLanguages();
    }

    /**
     * Convenience method to access current rendered language.
     *
     * @return string
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
     */
    public function getCommandQueue(): CommandQueue
    {
        if (null === $this->commandQueue) {
            $this->commandQueue = new CommandQueue();
        }

        return $this->commandQueue;
    }

    /**
     * Convenience (facade) method to add $command to CommandQueue.
     *
     * @param AbstractCommand $command
     *
     * @return AbstractProject
     * @throws \Exception
     */
    protected function addCommand(AbstractCommand $command): AbstractProject
    {
        $this->getCommandQueue()
             ->addCommand($command);

        return $this;
    }

    /**
     * Convenience (facade) method to add $commands array to CommandQueue.
     *
     * @param AbstractCommand[] $commands
     *
     * @return AbstractProject
     * @throws \Exception
     */
    protected function addCommands(array $commands): AbstractProject
    {
        foreach ($commands as $command) {
            if ($command instanceof AbstractCommand) {
                $this->addCommand($command);
            }
        }

        return $this;
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
     * @throws \Exception
     */
    public function addPageMessage(string $message, bool $onPage = false): AbstractProject
    {
        $this->getCommandQueue()
             ->addPageMessage($message, $onPage);

        return $this;
    }
}
