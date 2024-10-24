<?php
/**
 * mds Agenturgruppe GmbH
 *
 * This source file is licensed under GNU General Public License version 3 (GPLv3).
 *
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) mds. Agenturgruppe GmbH (https://www.mds.eu)
 */

namespace Mds\PimPrint\CoreBundle\InDesign\Command;

use Mds\PimPrint\CoreBundle\InDesign\Command\Traits\FileBoxScaledTrait;

/**
 * Class FileBoxScaled
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command
 */
class FileBoxScaled extends FileBox
{
    use FileBoxScaledTrait;

    /**
     * Available command params with default values.
     *
     * @var array
     */
    protected array $availableParams = [
        'fit'          => FileBox::FIT_NONE,
        'src'          => '',
        'assetId'      => '',
        'mtime'        => '',
        'thumbnailUrl' => '',
        'srcUrl'       => '',
    ];
}
