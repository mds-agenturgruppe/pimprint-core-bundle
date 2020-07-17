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

use Mds\PimPrint\CoreBundle\InDesign\Command\AbstractCommand;
use Mds\PimPrint\CoreBundle\InDesign\CommandQueue;
use Mds\PimPrint\CoreBundle\Project\Traits\HelpersTrait;
use Mds\PimPrint\CoreBundle\Project\Traits\RenderingTrait;
use Pimcore\Tool;

/**
 * Class AbstractProject
 *
 * @package Mds\PimPrint\CoreBundle\Project
 */
abstract class AbstractProject
{
    use HelpersTrait;
    use RenderingTrait;

    /**
     * Default PHP time_limit.
     *
     * @var int
     */
    const DEFAULT_TIME_LIMIT = 0;

    /**
     * Default PHP memory_limit.
     *
     * @var string
     */
    const DEFAULT_MEMORY_LIMIT = '2G';

    /**
     * CommandQueue instance.
     *
     * @var CommandQueue
     */
    private $commandQueue;

    /**
     * Array with messages displayed in InDesign Plugin before rendering.
     *
     * @var array
     */
    protected $preMessages = [];

    /**
     * Returns all publications in tree structure to display in InDesign-Plugin.
     *
     * @return array
     */
    abstract public function getPublicationsTree(): array;

    /**
     * Generates InDesign Commands to build the selected publication in InDesign.
     *
     * @return void
     */
    abstract public function buildPublication(): void;

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
        $return = [];
        foreach ($this->getUserLanguages() as $code) {
            $return[] = [
                'iso'   => $code,
                'label' => $translation = \Locale::getDisplayLanguage(
                    $code,
                    $this->userHelper->getUser()
                                     ->getLanguage()
                ),
            ];
        }

        return $return;
    }

    /**
     * Returns options for InDesign Plugin.
     * Method is in AbstractProject to allow project specific overwrites.
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->pluginParams->getOptions();
    }

    /**
     * Builds project settings for InDesign plugin.
     *
     * @return array
     * @throws \Exception
     */
    final public function getSettings()
    {
        return [
            'assets'   => [
                'download'    => $this->config()
                                      ->offsetGet('assets')['download'],
                'preDownload' => $this->config()
                                      ->offsetGet('assets')['preDownload']
            ],
            'template' => [
                'download' => $this->config()
                                   ->offsetGet('template')['download'],
            ],
        ];
    }

    /**
     * Returns InDesign template filename from configuration.
     * Can be overwritten in concrete projects to use values from Pimcore data model like fields or properties.
     *
     * @return string
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
     * Returns languages for current user.
     * For admin user all activated languages are returned.
     * Otherwise all assigned content languages are returned.
     *
     * Template method can be overwritten in concrete projects to have e.g. workspace languages used.
     *
     * @return array
     * @see \Mds\PimPrint\CoreBundle\Service\UserHelper::getVisibleWorkspaceLanguages
     */
    protected function getUserLanguages()
    {
        $user = Tool\Admin::getCurrentUser();
        if (true === $user->isAdmin()) {
            return Tool::getValidLanguages();
        } else {
            return $user->getContentLanguages();
        }
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
    public function addPreMessage(string $message)
    {
        $this->preMessages[] = $message;

        return $this;
    }

    /**
     * Returns pre rendering messages.
     *
     * @return array
     */
    public function getPreMessages()
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
    public function addPageMessage(string $message, $onPage = false)
    {
        $this->getCommandQueue()
             ->addPageMessage($message, $onPage);

        return $this;
    }
}
