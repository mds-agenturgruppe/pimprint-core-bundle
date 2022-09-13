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

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class UrlGeneratorAccessor
 *
 * @package Mds\PimPrint\CoreBundle\Service
 */
class UrlGeneratorAccessor
{
    /**
     * Pimcore UrlGenerator
     *
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * UrlGeneratorAccessor constructor
     *
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Returns Pimcore UrlGenerator
     *
     * @return UrlGeneratorInterface
     */
    public function getUrlGenerator(): UrlGeneratorInterface
    {
        return $this->urlGenerator;
    }
}
