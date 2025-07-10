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
