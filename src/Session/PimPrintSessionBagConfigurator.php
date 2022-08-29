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

namespace Mds\PimPrint\CoreBundle\Session;

use Pimcore\Session\SessionConfiguratorInterface;
use Symfony\Component\HttpFoundation\Session\Attribute\NamespacedAttributeBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class PimPrintSessionBagConfigurator
 *
 * @package Mds\PimPrint\CoreBundle\Session
 */
class PimPrintSessionBagConfigurator implements SessionConfiguratorInterface
{
    /**
     * Session namespace.
     *
     * @var string
     */
    const NAMESPACE = 'mds_pimprint';

    /**
     * Configure the session (e.g. register a bag)
     *
     * @param SessionInterface $session
     */
    public function configure(SessionInterface $session)
    {
        $bag = new NamespacedAttributeBag('_' . self::NAMESPACE);
        $bag->setName(self::NAMESPACE);

        $session->registerBag($bag);
    }
}
