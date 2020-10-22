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
 * Command for creating page layout elements in InDesign. All commands added to a template are executed automatically
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
    const IDENT_PREFIX = 'T-AUTO#';

    /**
     * Defines commands for use on single page documents.
     *
     * @var string
     */
    const SIDE_SINGLE = 'single';

    /**
     * Defines commands for use on facing page documents left and side.
     *
     * @var string
     */
    const SIDE_FACING_BOTH = 'both';

    /**
     * Defines commands for use on facing page documents left side.
     *
     * @var string
     */
    const SIDE_FACING_LEFT = 'left';

    /**
     * Defines commands for use on facing page documents right side.
     *
     * @var string
     */
    const SIDE_FACING_RIGHT = 'right';

    /**
     * {@inheritDoc}
     *
     * @var array
     */
    protected $availableParams = [
        'cmds_single'       => [],
        'cmds_facing_left'  => [],
        'cmds_facing_right' => [],
    ];

    /**
     * Adds $commands to template.
     *
     * @param AbstractCommand[] $commands
     * @param string            $side Single/Facing page documents side
     *
     * @return Template
     * @throws \Exception
     */
    public function addCommands(array $commands, $side = Template::SIDE_SINGLE)
    {
        foreach ($commands as $command) {
            if (false === $command instanceof AbstractCommand) {
                throw new \Exception(sprintf("Command must be instance of '%s'.", AbstractCommand::class));
            }
            $this->addCommand($command, $side);
        }

        return $this;
    }

    /**
     * Adds $command to template.
     *
     * @param AbstractCommand $command
     * @param string          $side Single/Facing page documents side
     *
     * @return Template
     * @throws \Exception
     */
    public function addCommand(AbstractCommand $command, $side = Template::SIDE_SINGLE)
    {
        $this->setGenericPostfix($side);
        $this->ensureBoxIdent($command);
        $this->collectImageCommand($command);
        switch ($side) {
            case Template::SIDE_SINGLE:
                $this->params['cmds_single'][] = $command->buildCommand();
                break;
            case Template::SIDE_FACING_BOTH:
                if ($command instanceof AbstractBox) {
                    $command->setBoxIdent();
                }
                $this->addCommand($command, self::SIDE_FACING_LEFT);
                if ($command instanceof AbstractBox) {
                    $command->setBoxIdent();
                }
                $this->addCommand($command, self::SIDE_FACING_RIGHT);
                break;
            case Template::SIDE_FACING_LEFT:
                $this->params['cmds_facing_left'][] = $command->buildCommand();
                break;
            case Template::SIDE_FACING_RIGHT:
                $this->params['cmds_facing_right'][] = $command->buildCommand();
                break;
        }
        if ($command instanceof AbstractBox) {
            $command->setBoxIdent();
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
        $this->params['cmds_single'] = [];
        $this->params['cmds_facing_left'] = [];
        $this->params['cmds_facing_right'] = [];

        return $this;
    }
}
