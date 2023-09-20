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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class PimPrintSessionBagConfigurator
 *
 * @package Mds\PimPrint\CoreBundle\Session
 */
class PimPrintSessionBagConfigurator implements EventSubscriberInterface
{
    /**
     * Session namespace.
     *
     * @var string
     */
    const NAMESPACE = 'mds_pimprint';

    /**
     * {@inheritDoc}
     *
     * @return array[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 127],
        ];
    }

    /**
     * Register session bag
     *
     * @param RequestEvent $event
     *
     * @return void
     *
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if ($event->getRequest()->attributes->get('_stateless', false)) {
            return;
        }

        $session = $event->getRequest()
                         ->getSession();

        if ($session->isStarted()) {
            return;
        }

        $bag = new AttributeBag('_' . self::NAMESPACE);
        $bag->setName(self::NAMESPACE);

        $session->registerBag($bag);
    }
}
