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

namespace Mds\PimPrint\CoreBundle\Service\AccessorTraits;

use Mds\PimPrint\CoreBundle\Service\UrlGeneratorAccessor;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

trait UrlGeneratorTrait
{
    /**
     * Returns Pimcore UrlGenerator
     *
     * @return UrlGeneratorInterface
     */
    protected function getUrlGenerator(): UrlGeneratorInterface
    {
        return \Pimcore::getKernel()
                       ->getContainer()
                       ->get(UrlGeneratorAccessor::class)
                       ->getUrlGenerator();
    }
}
