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

namespace Mds\PimPrint\CoreBundle\Security\Guard;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

/**
 * Class AbstractAuthenticator
 *
 * @package Mds\PimPrint\CoreBundle\Security\Guard
 */
abstract class AbstractAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * Returns true if $request comes from PimPrint InDesign-Plugin.
     *
     * @param Request $request
     *
     * @return bool
     */
    protected function isInDesignRequest(Request $request)
    {
        $header = $request->headers->get('mds-pimprint');

        return !empty($header);
    }
}
