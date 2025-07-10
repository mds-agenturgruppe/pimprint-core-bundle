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
