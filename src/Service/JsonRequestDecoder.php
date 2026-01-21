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
     *
     * @return void
     */
    public function decode(Request $request): void
    {
        if ('json' !== $request->getContentTypeFormat() || empty($request->getContent())) {
            return;
        }
        if ($request->attributes->has('__json_decoded')) {
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
