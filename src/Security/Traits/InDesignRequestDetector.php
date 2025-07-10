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

namespace Mds\PimPrint\CoreBundle\Security\Traits;

use Symfony\Component\HttpFoundation\Request;

/**
 * Trait to detect requests from PimPrint InDesign Plugin.
 *
 * @package Mds\PimPrint\CoreBundle\Security\Traits
 */
trait InDesignRequestDetector
{
    /**
     * Returns true if $request comes from PimPrint InDesign-Plugin.
     *
     * @param Request $request
     *
     * @return bool
     */
    protected function isInDesignRequest(Request $request): bool
    {
        $header = $request->headers->get('mds-pimprint');

        return !empty($header);
    }
}
