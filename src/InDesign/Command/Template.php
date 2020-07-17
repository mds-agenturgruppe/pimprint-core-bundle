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

namespace Mds\PimPrint\CoreBundle\InDesign\Command;

use Mds\PimPrint\CoreBundle\InDesign\Command\Traits\ImageCollectorTrait;
use Mds\PimPrint\CoreBundle\InDesign\Traits\BoxIdentBuilderTrait;

/**
 * Class Template
 *
 * Command representing a InDesign template. All commands added to a template are executed automatically
 * when a page is accessed in InDesign document.
 * If a page is accessed multiple times and the template is already applied to the page
 * the commands aren't executed once again.
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command
 */
class Template extends AbstractCommand implements ImageCollectorInterface
{
    use BoxIdentBuilderTrait;
    use ImageCollectorTrait;

    /**
     * Command name.
     *
     * @var string
     */
    const CMD = 'autopagecmds';

    /**
     * Prefix for BoxIdent.
     *
     * @var string
     */
    const TID_PREFIX = 'T';

    /**
     * Defines Commands for left and right side.
     *
     * @var string
     */
    const SIDE_BOTH = 'both';

    /**
     * Defines commands for left side.
     *
     * @var string
     */
    const SIDE_LEFT = 'left';

    /**
     * Defines commands for right side.
     *
     * @var string
     */
    const SIDE_RIGHT = 'right';

    /**
     * {@inheritDoc}
     *
     * @var array
     */
    protected $availableParams = [
        'cmds'       => [],
        'cmds_left'  => [],
        'cmds_right' => [],
    ];

    /**
     * Adds $commands to template.
     *
     * @param AbstractCommand[] $commands
     * @param string            $site use
     *
     * @return Template
     * @throws \Exception
     */
    public function addCommands(array $commands, $site = Template::SIDE_BOTH)
    {
        foreach ($commands as $command) {
            if (false === $command instanceof AbstractCommand) {
                throw new \Exception(sprintf("Command must be instance of '%s'.", AbstractCommand::class));
            }
            $this->addCommand($command, $site);
        }

        return $this;
    }

    /**
     * Adds $command to template.
     *
     * @param AbstractCommand $command
     * @param string          $site
     *
     * @return Template
     * @throws \Exception
     */
    public function addCommand(AbstractCommand $command, $site = Template::SIDE_BOTH)
    {
        $this->createBoxIdent($command);
        $this->collectImageCommand($command);
        switch ($site) {
            case Template::SIDE_BOTH:
                $this->params['cmds'][] = $command->buildCommand();
                break;
            case Template::SIDE_LEFT:
                $this->params['cmds_left'][] = $command->buildCommand();
                break;
            case Template::SIDE_RIGHT:
                $this->params['cmds_right'][] = $command->buildCommand();
                break;
        }

        return $this;
    }

    /**
     * Clears all commands in template.
     *
     * @return Template
     */
    public function clear()
    {
        $this->params['cmds'] = [];
        $this->params['cmds_left'] = [];
        $this->params['cmds_right'] = [];

        return $this;
    }
}
