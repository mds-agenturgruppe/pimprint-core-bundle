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

namespace Mds\PimPrint\CoreBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class JsonRequestDecoder
 *
 * @package Mds\PimPrint\CoreBundle\Service
 */
class JsonRequestDecoder
{
    /**
     * Convert POST JSON request content and adds content to $request parameter bag.
     *
     * @param Request $request
     */
    public function decode(Request $request)
    {
        if ('json' !== $request->getContentType() || empty($request->getContent())) {
            return;
        }
        if (true === $request->attributes->has('__json_decoded')) {
            return;
        }
        $data = json_decode($request->getContent(), true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new BadRequestHttpException('Invalid json body: ' . json_last_error_msg());
        }
        $request->request->replace(is_array($data) ? $data : []);
        $request->attributes->set('__json_decoded', true);
    }
}
